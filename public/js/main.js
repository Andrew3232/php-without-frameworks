$(document).ready(() => {
    var post_template = $('#post_template').clone().html();
    $('#post_template').remove();

    const toastSuccessTrigger = document.getElementById('toastSuccessBtn');
    const toastSuccess = document.getElementById('toastSuccess');
    if (toastSuccessTrigger) {
        const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastSuccess);
        toastSuccessTrigger.addEventListener('click', () =>
        {
            toastBootstrap.show();
        });
    }

    const toastErrorTrigger = document.getElementById('toastErrorBtn');
    const toastError = document.getElementById('toastError');
    if (toastErrorTrigger) {
        const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastError);
        toastErrorTrigger.addEventListener('click', () =>
        {
            toastBootstrap.show();
        });
    }

    $(document).on('submit', '#add_post_form', function(e) {
        e.preventDefault();
        let $this = $(this);
        $.ajax({
            url: 'posts/create.php',
            method: 'POST',
            dataType: 'json',
            data: $this.serialize(),
            beforeSend: function()
            {
                $this.find('.old-label').addClass('d-none');
                $this.find('.spinner-border').removeClass('d-none');
                $this.find('.loading-label').removeClass('d-none');
                $this.find('button[type="submit"]').prop('disabled', true);
            },
            success: function(result)
            {
                $this.find('.old-label').removeClass('d-none');
                $this.find('.spinner-border').addClass('d-none');
                $this.find('.loading-label').addClass('d-none');
                $this.find('button[type="submit"]').prop('disabled', false);
                if (result.status === 'success') {
                    let post = result.post;
                    let post_html = post_template;
                    post_html = post_html.toString().
                        replaceAll('#title', post.title).
                        replaceAll('#id', post.id).
                        replaceAll('#text', post.text).
                        replaceAll('#time', result.time);
                    $('#posts_container').prepend(post_html);
                    if($('#posts_container').length) {
                        $('#empty_posts').addClass('d-none');
                    }
                    $('#addPost .btn-close').click();
                    $this.each(function()
                    {
                        this.reset();
                    });
                    $('#toastSuccess .toast-body').text(result.message);
                    toastSuccessTrigger.click();
                } else
                {
                    $('#toastError .toast-body').text(result.errors.join('<br>'));
                    toastErrorTrigger.click();
                }
            },
        });
    });

    $(document).on('click', '.update-post-btn', function(e) {
        e.preventDefault();
        let $this = $(this);
        let id = $this.data('id').toString();
        let updateForm = $('#update_post_form');
        $.ajax({
            url: 'posts/find.php',
            method: 'GET',
            dataType: 'json',
            data: {'id': id},
            success: function(result)
            {
                if (result.status === 'success')
                {
                    let post = result.post;
                    updateForm.find('input[name="id"]').val(post.id);
                    updateForm.find('input[name="title"]').val(post.title);
                    updateForm.find('textarea[name="text"]').val(post.text);
                    $('#updatePostModalBtn').click();
                } else
                {
                    $('#toastError .toast-body').text(result.errors.join('<br>'));
                    toastErrorTrigger.click();
                }
            },
        });
    });

    $(document).on('submit', '#update_post_form', function(e) {
        e.preventDefault();
        let $this = $(this);
        $.ajax({
            url: 'posts/update.php?' + $this.serialize(),
            method: 'PUT',
            dataType: 'json',
            beforeSend: function()
            {
                $this.find('.old-label').addClass('d-none');
                $this.find('.spinner-border').removeClass('d-none');
                $this.find('.loading-label').removeClass('d-none');
                $this.find('button[type="submit"]').prop('disabled', true);
            },
            success: function(result)
            {
                $this.find('.old-label').removeClass('d-none');
                $this.find('.spinner-border').addClass('d-none');
                $this.find('.loading-label').addClass('d-none');
                $this.find('button[type="submit"]').prop('disabled', false);
                if (result.status === 'success')
                {
                    let post = result.post;
                    let post_html = post_template;
                    post_html = post_html.toString().
                        replaceAll('#title', post.title).
                        replaceAll('#id', post.id).
                        replaceAll('#text', post.text).
                        replaceAll('#time', result.time);

                    $(document).find('#posts_container #post-' + post.id).replaceWith(post_html);
                    $('#updatePost .btn-close').click();
                    $('#toastSuccess .toast-body').text(result.message);
                    toastSuccessTrigger.click();
                } else
                {
                    $('#toastError .toast-body').text(result.errors.join('<br>'));
                    toastErrorTrigger.click();
                }
            },
        });
    });

    $(document).on('click', '.remove-post-btn', function(e){
        e.preventDefault();
        let $this = $(this);
        let id = $this.data('id').toString();
        $.ajax({
            url: 'posts/delete.php?' + $.param({'id': id}),
            method: 'DELETE',
            dataType: 'json',
            beforeSend: function()
            {
            },
            success: function(result)
            {
                if (result.status === 'success')
                {
                    if (toastSuccessTrigger)
                    {
                        $('#toastSuccess .toast-body').text(result.message);
                        toastSuccessTrigger.click();
                    }
                    $this.closest('.card').fadeOut();
                    if($('#posts_container').length === 0) {
                        $('#empty_posts').removeClass('d-none');
                    }
                } else
                {
                    if (toastErrorTrigger)
                    {
                        $('#toastError .toast-body').text(result.message);
                        toastErrorTrigger.click();
                    }
                }
            },
        });
    });
});