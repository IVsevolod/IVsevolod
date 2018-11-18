$(document).ready(function(){
    function validateForm($form) {
        var result = true;
        $form.find('input').each(function() {
            var inpObj = this;
            if (!inpObj.checkValidity()) {
                result = false;
                console.log(inpObj.validationMessage);
            } else {

            }
        });
        return result;
    }

    $(document)
        .on('click', '.js--quest-action', function () {
            var $this = $(this);
            var $form = $('#questForm');
            if ($form.data('submitted')) {
                return false;
            }
            $form.find('input[name="action"]').val($this.data('action'));
            if (!$this.data('validate') || validateForm($form)) {
                var allData = $this.data();
                $.each(allData, function (index, value) {
                    if (index !== "action") {
                        $form.append($('<input type="hidden" name="' + index + '"/>').attr('value', value));
                    }
                });
                $form.submit();
                $form.data('submitted', true);
            }
        })
        .ajaxSend(function() {
            $('.block-loader').removeClass('hide');
        })
        .ajaxComplete(function() {
            $('.block-loader').addClass('hide');
        })
    ;

});