if (typeof jQuery === "undefined") {
    throw new Error("jQuery plugins need to be before this file");
}


$(function(){

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function () {
        $("#initTable")
            .addClass("nowrap")
            .dataTable({
                responsive: false,
                stateSave: true,
                columnDefs: [{ targets: [-1, -3], className: "dt-body-right" }],
            });
    });

    $(document).ready(function () {
        $("#initResponsiveTable")
            .addClass("nowrap")
            .dataTable({
                responsive: true,
                stateSave: true,
                columnDefs: [{ targets: [-1, -3], className: "dt-body-right" }],
            });
    });


    // ==============SELECT 2=============
    $(".select2").select2({
        // theme: "bootstrap5",
        allowClear: true,
        placeholder: "Choose an option",
    });

    $(".select2-multiple").select2({
        // theme: 'bootstrap4',
        placeholder: "Choose options",
        allowClear: true,
    });

    $(".select2-tags").select2({
        // theme: 'bootstrap4',
        placeholder: "Choose options",
        allowClear: true,
        tags: true,
    });

    $(".select2-multiple-tags").select2({
        // theme: 'bootstrap4',
        placeholder: "Choose options",
        allowClear: true,
        tags: true,
        multiple: true,
    });

    // datetime
    $('.datetime').datetimepicker({
        format:'d/m/Y H:i',
        maxDate: new Date(),
    });

    $('.date').datetimepicker({
        timepicker:false,
        format:'d/m/Y',
        maxDate: new Date(),
    });

    $('.year').datetimepicker({
        timepicker:false,
        format:'Y',
    });

    // Tooltip
    $('[data-bs-toggle="tooltip"]').tooltip();


    // select and preview founder
    $(document).on('change', ".passport_picture", function(){

        let inputFile = $(this);

        if (checkFile(inputFile.val())) {

            if (inputFile.val().substring(inputFile.val().lastIndexOf('.') + 1).toLowerCase() === 'pdf') {
                $('.passport_picture_preview').hide();
            }else{
                let reader = new FileReader();
                reader.onload = (e) => {
                    inputFile.parents('.image-box').find('.passport_picture_preview').show().attr('src', e.target.result).css({'width':'200px', 'height':'137px', 'object-fit':'cover'});
                }

                reader.readAsDataURL(this.files[0]);
            }


        }else{
            $(this).val('');
            toastr.error('File is not supported. Only png, jpg and jpeg images and pdf documents are supported');
        }
    })

    $(document).on('change', ".id_card", function(){
        let inputFile = $(this);

        if (checkFile(inputFile.val())) {

            if (inputFile.val().substring(inputFile.val().lastIndexOf('.') + 1).toLowerCase() == 'pdf') {

                $('.id_card_preview').hide();

            }else{
                let reader = new FileReader();
                reader.onload = (e) => {
                    inputFile.parents('.image-box').find('.id_card_preview').show().attr('src', e.target.result).css({'width':'200px', 'height':'137px', 'object-fit':'cover'});
                }

                reader.readAsDataURL(this.files[0]);
            }


        }else{
            $(this).val('');
            toastr.error('File is not supported. Only png, jpg and jpeg images and pdf documents are supported');
        }
    })

    $(document).on('change', "#status", function(){

        if($(this).val() == 'Released'){
            $('#releasedDateBox').show();
            // $('#released_date').attr('required', 'required');
        }else{
            $('#releasedDateBox').hide();
            // $('#released_date').removeAttr('required', 'required');
        }
    })

    $(document).on('change', "#status", function(){

        if($(this).val() == 'Move to trash'){
            $('.status-info').show();
        }else{
            $('.status-info').hide();
        }
    })


    $(document).on('change', ".document_type", function(){

        // Get the data-name attribute of the selected option
        var selectedName = $(this).find('option:selected').data('name');

        // Define the array of valid names
        var validNames = ['Civil Servant', 'Business Certificate', 'Private Payslip', 'Valuation Report'];

        // Check if the selectedName is in the validNames array
        if (validNames.includes(selectedName)) {
            $(this).parents('.surety-box').find('.payslip-box').show();
            $('.selected_document_name').html(selectedName);
        } else {
            $(this).parents('.surety-box').find('.payslip-box').hide();
        }
    });



// check image upload
    function checkFile(val){
        let valid;
        switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
            case 'jpeg': case 'jpg': case 'png': case 'pdf':
                valid =  true;
                break;

            default:
                valid = false;
                break;
        }

        return valid;
    }


    $(document).on('change', '#court_type', function()
    {

        let options = ['<option></option>'];

        $.ajax({
            url: "/locations/fetch",
            type: "POST",
            dataType:"json",
            data: { court_type: $('#court_type').val() },
            success: function (response) {
                $.each(response, function(index, item){

                    let option = '<option value="'+item.id+'">'+item.name+'</option>';

                    options.push(option);
                })

                $('#location').empty().append(options).trigger('change');
            },
            error: function () {
                toastr.error('An error occurred during request.');
            }
        });
    })

    $(document).on('change', '#location', function() {
        let options = ['<option></option>'];
        $.ajax({
            url: "/registry/fetch",
            type: "POST",
            dataType:"json",
            data: { location: $(this).val() },
            success: function (response) {
                $.each(response, function(index, item){
                    let option = '<option value="'+item.id+'">'+item.name+'</option>';

                    options.push(option);
                })

                $('#registry').empty().append(options);
            },
            error: function () {
                toastr.error('An error occurred during request.');
            }
        });
    })


    $(document).on('change', '.document_number', function() {

        let input = $(this);

        let info =  input.parents('.document-box');
        info.find('.document-message-error').hide();
        info.find('.document-message-success').hide();

        info.find('.document-info').show();

        $.ajax({
            url: "/validations/verify-document",
            type: "POST",
            dataType:"json",
            data: { document: input.val() },
            success: function (response) {

                if(response.success){

                    $('.create-bail').removeAttr('disabled', 'disabled');
                    setTimeout(function () {
                        info.find('.document-info').hide();
                        info.find('.document-message-error').hide();
                        info.find('.document-message-success').html(response.success).show();
                    }, 1000);

                }else{

                    $('.create-bail').attr('disabled', 'disabled');
                    toastr.error(response.error);
                    setTimeout(function () {
                        info.find('.document-info').hide();
                        info.find('.document-message-success').hide();
                        info.find('.document-message-error').html(response.error).show();
                    }, 1000);

                }
            },
            error: function () {
                info.find('.document-info').hide();
                toastr.error('An error occurred during request.');
            }
        });
    })

// $('.create-bail').attr('disabled', 'disabled');

    $(document).on('click', '.create-bail', function(){
        let button = $(this);
        button.html('<i class="fas fa-spin fa-spinner"></i> Creating bail...');

        // Get form data using FormData
        let formData = new FormData($('#bailForm')[0]);

        $.ajax({
            url: '/bail/create',
            type: 'POST',
            data: formData,
            // Prevent jQuery from processing data
            processData: false,
            // Prevent jQuery from setting content type
            contentType: false,
            success: function(data){
                if (data.success) {
                    toastr.success(data.success);
                    window.location.reload();
                } else if(data.url) {
                    toastr.success('Bail created successfully. You can now validate document!');
                    // console.log(data);
                    window.location.href = data.url;
                } else {
                    button.html('<i class="fas fa-save"></i> Create bail');
                    toastr.error(data.error);
                }
            },
            error: function(xhr, status, error){
                button.html('<i class="fas fa-save"></i> Create bail');
                toastr.error('An error occurred while processing the request.');
                // console.error(xhr.responseText);
            }
        });
    });


    $(document).on('click', '.update-bail', function(e){
        // $(document).on('submit', '#bailForm', function(e){
        e.preventDefault();
        let button = $(this);
        button.html('<i class="fas fa-spin fa-spinner"></i> Saving changes...');

        // Get form data using FormData
        let formData = new FormData($('#bailForm')[0]);
        let slug = $('slug').val();

        $.ajax({
            url: '/bail/'+slug+'/edit',
            type: 'POST',
            data: formData,
            // Prevent jQuery from processing data
            processData: false,
            // Prevent jQuery from setting content type
            contentType: false,
            success: function(data){
                if (data.bail_url) {
                    toastr.success('Bail changes saved successfully.');
                    window.location.href = data.bail_url;
                } else if(data.url) {
                    toastr.warning('Bail moved to trash successfully.');
                    window.location.href = data.url;
                } else {
                    button.html('<i class="fas fa-save"></i> Save changes');
                    toastr.error(data.error);
                }
            },
            error: function(xhr, status, error){
                button.html('<i class="fas fa-save"></i> Save changes');
                toastr.error('An error occurred while processing the request.');
                // console.error(xhr.responseText);
            }
        });
    });




    $(document).on('click', '.view_bail', function(){

        let btn = $(this);

        let slug = btn.data('slug');
        // console(slug);
        btn.siblings(".show-spinner").fadeIn();

        $.post('/bail/fetch-content', {slug: slug}, function(data){

            $('.bail-modal-content').html(data);

            setTimeout(function(){
                btn.siblings(".show-spinner").fadeOut();
                $('#bailModal').modal('show');
            }, 1000);
        })
    })

    $(document).on('click', '#releaseBail', function(){

        if (confirm('Are you sure you want to RELEASE this bail?')) {
            let btn = $(this);

            let slug = btn.data('slug');

            btn.html('<i class="fas fa-spin fa-spinner"></i> Releasing...');

            $.post('/bail/release', {slug: slug}, function (data) {

                if (data.success) {
                    btn.hide();
                    $('.show_released').show()
                    toastr.success(data.success);

                    livewire.emit('reloadComponent');

                    window.location.reload();

                } else {
                    btn.html('<i class="fas fa-check"></i> Release bail').show();
                    toastr.error(data.error);
                }
            })
        }
    })


    $(document).on('click', '.add-surity', function(){

        $('.surety-loader').show();

        $.post('/bail/fetch-surety', function(data){

            $('.surety-loader').hide();

            $('#surety-content').append(data);
            $('#surety-content').find('.select2').select2({
                allowClear: true,
                placeholder: "Choose an option",
            });
        })

        if ($('.surety-box').length >= 2){
            $('.add-surity').hide();
        }
    })

    $(document).on('click', '.remove-surity', function(){

        $(this).parents('.surety-box').remove();

        if ($('.surety-box').length <=  2){
            $('.add-surity').show();
        }
    })

    $(document).on('click', '.delete-surety', function(){
        // Show confirmation dialog
        if (confirm('Are you sure you want to delete this surety?')) {
            // Proceed with the AJAX request if the user confirms
            $.post('/bail/delete-surety', {'slug': $(this).data('slug')}, function(data){
                if(data.url){
                    window.location.reload();
                }else{
                    toastr.error(data.error);
                }
            });
        }
    });

    $(document).on('click', '#releaseSurety', function(){

        if (confirm('Are you sure you want to RELEASE this surety document?')) {
            let btn = $(this);

            let slug = btn.data('slug');

            btn.html('<i class="fas fa-spin fa-spinner"></i> Releasing...');

            $.post('/surety/release', {slug: slug}, function (data) {

                if (data.success) {
                    toastr.success(data.success);
                    btn.html('<i class="fas fa-check"></i> Release Document');
                    window.location.reload();
                } else {
                    btn.html('<i class="fas fa-check"></i> Release Document');
                    toastr.error(data.error);
                }
            })
        }
    })


    $(document).on('click', '#checkAll', function(){
        if ($(this).is(':checked')) {
            $('.singleCheck').prop('checked', true);
        }else{
            $('.singleCheck').prop('checked', false);
        }
    })

    $(document).on('click', '.singleCheck', function(){
        enableCheckAll();
    })


    function getCheckedItemCount(){

        let checkboxes = $('.singleCheck');

        let totalChecked = 0;
        $.each(checkboxes, function(index, item){
            // console.log()
            if(item.checked == true){
                totalChecked++
            }
        })

        return totalChecked;
    }

    enableCheckAll();

    function enableCheckAll(){

        if (getCheckedItemCount() == $('.singleCheck').length) {
            $('#checkAll').prop('checked', true);
        }else{
            $('#checkAll').prop('checked', false);
        }
    }


//clear input on audit trail page
    $(document).on("click", ".clear-search", function (e) {
        e.preventDefault();

        $("input").not('[name="_token"]').val("");
        $(".select2").val(null).trigger("change");

        var uri = window.location.href.toString();
        if (uri.indexOf("?") > 0) {
            var clean_uri = uri.substring(0, uri.indexOf("?"));
            window.history.replaceState({}, document.title, clean_uri);
        }
        // $("#cases_table").load(location.href + " #cases_table");
        window.location.reload();
    });


    $(document).on("click", "#is_expire", function (e) {
        if($(this).is(":checked")){
            $('.expire_date').show();
            $('#expire_date').attr('required', 'required');
        }else{
            $('.expire_date').hide();
            $('#expire_date').removeAttr('required');
        }
    })



    $(document).on('click', '.releaseBail', function(){

        $('#release_slug').val($(this).data('slug'));

        $("#releaseBailModal").modal('show');
    })


    $(document).on('click', '#proceedRelease', function(){

        let btn = $(this);

        let slug = $('#release_slug').val();

        btn.html('<i class="fas fa-spin fa-spinner"></i> Releasing...');

        $.post('/bail/release', {slug: slug}, function(data){

            if(data.success){
                // $("#releaseBailModal").modal('hide');
                window.location.reload();
                toastr.success(data.success);

            }else{
                btn.html('<i class="fas fa-check"></i> Release bail').show();
                toastr.error(data.error);
            }
        })
    })


    $(document).on('keydown', '#suit_number', function(event){

        var allowedCharacters = /^[a-zA-Z0-9\/]*$/;
        var key = event.key;

        // Allow navigation keys (arrow keys, backspace, delete, etc.)
        if (event.ctrlKey || event.altKey || event.metaKey || event.key === 'ArrowLeft' || event.key === 'ArrowRight' || event.key === 'Backspace' || event.key === 'Delete') {
            return;
        }

        // Prevent input if the key is not allowed
        if (!allowedCharacters.test(key)) {
            event.preventDefault();
        }
    })

})
