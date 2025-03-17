jQuery(document).ready(function($) {
    $('select[name="user_tags[]"]').select2({
        placeholder: "Select User Tags",
        allowClear: true
    });
});
