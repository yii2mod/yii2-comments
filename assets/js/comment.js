/**
 * Comment plugin
 */
(function ($) {
    $.comment = function (method) {
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
        cancelReplyBtnSelector: '#cancel-reply'
    };

    // Methods
    var methods = {
        init: function (options) {
            if ($.data(document, 'comment') !== undefined) {
                return;
            }
            // Set plugin data
            $.data(document, 'comment', $.extend({}, defaults, options || {}));
            return this;
        },
        data: function () {
            return $.data(document, 'comment');
        }
    };


    /**
     * Reply to comment
     */
    $(document).on('click', '[data-action="reply"]', function (event) {
        event.preventDefault();
        var data = $.data(document, 'comment');
        var $this = $(this);
        var parentCommentSelector = $this.parents('[data-comment-content-id="' + $this.data('comment-id') + '"]');
        var $form = $(data.formSelector);
        //Move form to comment container
        $form.appendTo(parentCommentSelector);
        //Update parentId field
        $form.find('[data-comment="parent-id"]').val($this.data('comment-id'));
        //Show cancel reply link
        $(data.cancelReplyBtnSelector).show();
    });

    /**
     * Cancel reply
     */
    $(document).on('click', '[data-action="cancel-reply"]', function (event) {
        event.preventDefault();
        var data = $.data(document, 'comment');
        $(data.cancelReplyBtnSelector).hide();
        var $form = $(data.formSelector);
        //Move form to form container
        $form.prependTo(data.formContainerSelector);
        //Update parentId field
        $form.find('[data-comment="parent-id"]').val(null);
    });

    /**
     * Delete comment event
     */
    $(document).on('click', '[data-action="delete"]', function (event) {
        event.preventDefault();
        var data = $.data(document, 'comment');
        var $this = $(this);
        $.ajax({
            url: $this.data('url'),
            type: 'DELETE',
            error: function (xhr, status, error) {
                alert(error);
            },
            success: function (result, status, xhr) {
                $this.parents('[data-comment-content-id="' + $this.data('comment-id') + '"]').find(data.contentSelector).text(result);
                $this.parents(data.toolsSelector).remove();
            }
        });
    });

})(window.jQuery);