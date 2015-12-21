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
        // Comment actions buttons selector
        toolsSelector: '.comment-action-buttons',
        // Form selector
        formSelector: '#comment-form',
        // Form container selector
        formContainerSelector: '.comment-form-container',
        // Comment content selector
        contentSelector: '.comment-body',
        // Cancel reply button selector
        cancelReplyBtnSelector: '#cancel-reply',
        //Pjax container id
        pjaxContainerId: '#comment-pjax-container',
        //Pjax default settings
        pjaxSettings: {
            timeout: 10000,
            scrollTo: false,
            url: window.location.href
        }
    };

    // Methods
    var methods = {
        init: function (options) {
            return this.each(function () {
                var $commentForm = $(this);
                if ($commentForm.data('comment')) {
                    return;
                }
                var settings = $.extend({}, defaults, options || {});
                $commentForm.data('comment', settings);
                //Add events
                $commentForm.on('beforeSubmit.comment', beforeSubmitForm);
                var eventParams = {commentForm: $commentForm};
                $(settings.pjaxContainerId).on('click.comment', '[data-action="reply"]', eventParams, reply);
                $(settings.pjaxContainerId).on('click.comment', '[data-action="cancel-reply"]', eventParams, cancelReply);
                $(settings.pjaxContainerId).on('click.comment', '[data-action="delete"]', eventParams, deleteComment);
            });
        },
        data: function () {
            return this.data('comment');
        },
        reset: function (settings) {
            $(settings.pjaxContainerId).each(function () {
                $(this).unbind('.comment');
                $(this).removeData('comment');
            });
            $(settings.formSelector).comment(settings);
        }
    };


    /**
     * This function used for `beforeSubmit` comment form event
     */
    function beforeSubmitForm() {
        var $commentForm = $(this),
            settings = $commentForm.data('comment');
        //Add loading to comment button
        $commentForm.find(':submit').prop('disabled', true).text('Loading...');
        var pjaxSettings = $.extend({container: settings.pjaxContainerId}, settings.pjaxSettings);
        //Send post request
        $.post($commentForm.attr("action"), $commentForm.serialize(), function (data) {
            //If success is status, then pjax container has been reloaded and comment form has been reset
            if (data.status == 'success') {
                $.pjax(pjaxSettings).done(function () {
                    $commentForm.find(':submit').prop('disabled', false).text('Comment');
                    $commentForm.trigger("reset");
                    //Restart plugin
                    methods.reset.call($commentForm, settings);
                });
            }
            //If status is error, then only show form errors.
            else {
                if (data.hasOwnProperty('errors')) {
                    $commentForm.yiiActiveForm('updateMessages', data.errors, true);
                }
                else {
                    $commentForm.yiiActiveForm('updateAttribute', 'commentmodel-content', [data.message]);
                }
                $commentForm.find(':submit').prop('disabled', false).text('Comment');
            }
        });
        return false;
    }

    /**
     * Reply to comment
     * @param event
     */
    function reply(event) {
        event.preventDefault();
        var $commentForm = event.data.commentForm;
        var settings = $commentForm.data('comment');
        var $this = $(this);
        var parentCommentSelector = $this.parents('[data-comment-content-id="' + $this.data('comment-id') + '"]');
        //Move form to comment container
        $commentForm.appendTo(parentCommentSelector);
        //Update parentId field
        $commentForm.find('[data-comment="parent-id"]').val($this.data('comment-id'));
        //Show cancel reply link
        $commentForm.find(settings.cancelReplyBtnSelector).show();
    }

    /**
     * Cancel reply
     * @param event
     */
    function cancelReply(event) {
        event.preventDefault();
        var $commentForm = event.data.commentForm;
        var settings = $commentForm.data('comment');
        $commentForm.find(settings.cancelReplyBtnSelector).hide();
        //Move form back to form container
        var formContainer = $(settings.pjaxContainerId).find(settings.formContainerSelector);
        $commentForm.prependTo(formContainer);
        //Update parentId field
        $commentForm.find('[data-comment="parent-id"]').val(null);
    }

    /**
     * Delete comment
     * @param event
     */
    function deleteComment(event) {
        event.preventDefault();
        var $commentForm = event.data.commentForm;
        var settings = $commentForm.data('comment');
        var $this = $(this);
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
    }

})(window.jQuery);
