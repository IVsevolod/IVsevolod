$(document).ready(function(){
    $(document)
        .on('click', '.js--quest-action', function () {
            var $this = $(this);
            var $form = $('#questForm');
            if ($form.data('submitted')) {
                return false;
            }
            $form.find('input[name="action"]').val($this.data('action'));
            if (!$this.data('validate') || $form.valid()) {
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
    ;

});