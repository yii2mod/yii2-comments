/**
 * Comment plugin
 */
(function ($) {

    $.fn.comment = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.comment');
            return false;
        }
    };

    // Default settings
    var defaults = {
        toolsSelector: '.comment-action-buttons',
        formSelector: '#comment-form',
        formContainerSelector: '.comment-form-container',
        contentSelector: '.comment-body',
        cancelReplyBtnSelector: '#cancel-reply',
        pjaxContainerId: '#comment-pjax-container',
        pjaxSettings: {
            timeout: 10000,
            scrollTo: false,
            url: window.location.href
        },
        submitBtnText: 'Comment',
        submitBtnLoadingText: 'Loading...'
    };

    var events = {
        /**
         * beforeCreate event is triggered before creating the comment.
         * The signature of the event handler should be:
         *     function (event)
         * where
         *  - event: an Event object.
         *
         * If the handler returns a boolean false, it will stop further comment creation after this event.
         */
        beforeCreate: 'beforeCreate',
        /**
         * afterCreate event is triggered after comment has been created.
         * The signature of the event handler should be:
         *     function (event)
         * where
         *  - event: an Event object.
         */
        afterCreate: 'afterCreate',
        /**
         * beforeDelete event is triggered before comment has been deleted.
         * The signature of the event handler should be:
         *     function (event)
         * where
         *  - event: an Event object.
         *
         * If the handler returns a boolean false, it will stop further comment deletion after this event.
         */
        beforeDelete: 'beforeDelete',
        /**
         * afterDelete event is triggered after comment has been deleted.
         * The signature of the event handler should be:
         *     function (event)
         * where
         *  - event: an Event object.
         */
        afterDelete: 'afterDelete',
        /**
         * beforeReply event is triggered before reply to comment.
         * The signature of the event handler should be:
         *     function (event)
         * where
         *  - event: an Event object.
         *
         * If the handler returns a boolean false, it will stop further comment reply after this event.
         */
        beforeReply: 'beforeReply',
        /**
         * beforeReply event is triggered after reply to comment.
         * The signature of the event handler should be:
         *     function (event)
         * where
         *  - event: an Event object.
         */
        afterReply: 'afterReply'
    };

    var commentData = {};

    // Methods
    var methods = {
        init: function (options) {
            return this.each(function () {
                var $comment = $(this);
                var settings = $.extend({}, defaults, options || {});
                var id = $comment.attr('id');
                if (commentData[id] === undefined) {
                    commentData[id] = {};
                } else {
                    return;
                }
                commentData[id] = $.extend(commentData[id], {settings: settings});

                var formSelector = commentData[id].settings.formSelector;
                var eventParams = {formSelector: formSelector, wrapperSelector: id};

                $comment.on('beforeSubmit.comment', formSelector, eventParams, createComment);
                $comment.on('click.comment', '[data-action="reply"]', eventParams, reply);
                $comment.on('click.comment', '[data-action="cancel-reply"]', eventParams, cancelReply);
                $comment.on('click.comment', '[data-action="delete"]', eventParams, deleteComment);
            });
        },
        data: function () {
            var id = $(this).attr('id');
            return commentData[id];
        }
    };


    /**
     * Create a comment
     */
    function createComment(params) {
        var $commentForm = $(this);

        var event = $.Event(events.beforeCreate);
        $commentForm.trigger(event);
        if (event.result === false) {
            return false;
        }

        var settings = commentData[params.data.wrapperSelector].settings;
        var pjaxSettings = $.extend({container: settings.pjaxContainerId}, settings.pjaxSettings);
        var formData = $commentForm.serializeArray();
        formData.push({'name': 'CommentModel[url]', 'value': getCurrentUrl()});
        $commentForm.find(':submit').prop('disabled', true).text(settings.submitBtnLoadingText);

        $.post($commentForm.attr('action'), formData, function (data) {
            if (data.status === 'success') {
                $.pjax(pjaxSettings);

                $commentForm.trigger($.Event(events.afterCreate));
            }
            // errors handling
            else {
                if (data.hasOwnProperty('errors')) {
                    $commentForm.yiiActiveForm('updateMessages', data.errors, true);
                }
                else {
                    $commentForm.yiiActiveForm('updateAttribute', 'commentmodel-content', [data.message]);
                }
                $commentForm.find(':submit').prop('disabled', false).text(settings.submitBtnText);
            }
        }).fail(function (xhr, status, error) {
            alert(error);
            $.pjax(pjaxSettings);
        });

        return false;
    }

    /**
     * Reply to comment
     *
     * @param params
     */
    function reply(params) {
        var $this = $(this);
        var $commentForm = $(params.data.formSelector);

        var event = $.Event(events.beforeReply);
        $commentForm.trigger(event);
        if (event.result === false) {
            return false;
        }

        var settings = commentData[params.data.wrapperSelector].settings;
        var parentCommentSelector = $this.parents('[data-comment-content-id="' + $this.data('comment-id') + '"]');
        // append the comment form inside particular comment container
        $commentForm.appendTo(parentCommentSelector);
        $commentForm.find('[data-comment="parent-id"]').val($this.data('comment-id'));
        $commentForm.find(settings.cancelReplyBtnSelector).show();

        $commentForm.trigger($.Event(events.afterReply));

        return false;
    }

    /**
     * Cancel reply
     *
     * @param params
     */
    function cancelReply(params) {
        var $commentForm = $(params.data.formSelector);
        var settings = commentData[params.data.wrapperSelector].settings;
        var formContainer = $(settings.pjaxContainerId).find(settings.formContainerSelector);
        // prepend the comment form to `formContainer`
        $commentForm.find(settings.cancelReplyBtnSelector).hide();
        $commentForm.prependTo(formContainer);
        $commentForm.find('[data-comment="parent-id"]').val(null);

        return false;
    }

    /**
     * Delete a comment
     *
     * @param params
     */
    function deleteComment(params) {
        var $this = $(this);
        var $commentForm = $(params.data.formSelector);
        var settings = commentData[params.data.wrapperSelector].settings;

        var event = $.Event(events.beforeDelete);
        $commentForm.trigger(event);
        if (event.result === false) {
            return false;
        }

        $.ajax({
            url: $this.data('url'),
            type: 'DELETE',
            error: function (xhr, status, error) {
                alert(error);
            },
            success: function (result, status, xhr) {
                $this.parents('[data-comment-content-id="' + $this.data('comment-id') + '"]').find(settings.contentSelector).text(result);
                $this.parents(settings.toolsSelector).remove();
            }
        });

        $commentForm.trigger(events.afterDelete);

        return false;
    }

    /**
     * Get current url without `hostname`
     * @returns {string}
     */
    function getCurrentUrl() {
        return window.location.pathname + window.location.search;
    }

})(window.jQuery);
