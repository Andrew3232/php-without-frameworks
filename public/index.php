<?php

use App\Models\Post;
use Carbon\Carbon;
require_once __DIR__ . '/../vendor/autoload.php';

$posts = Post::all('id','desc');
$title = 'Home';

require __DIR__ . '/../include/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between mb-4">
        <h1>Posts</h1>
        <button type="button" class="btn btn-primary btn-sm w-25" style="max-width: 200px; min-width: fit-content;" data-bs-toggle="modal" data-bs-target="#addPost">+ Add post</button>
    </div>

    <div class="mt-2 mb-2">
        <p class="text-secondary text-center mt-2 <?if(!empty($posts)) echo 'd-none';?>" id="empty_posts">No posts added yet</p>
    </div>
    <div class="d-grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-4" id="posts_container">
        <?foreach($posts as $post):?>
            <div class="card h-100" id="post-<?=$post->id?>">
                <div class="card-body">
                    <h5 class="card-title"><?=$post->title?></h5>
                    <p class="card-text"><?=$post->text?></p>
                    <p class="card-text"><small class="text-body-secondary"><?=Carbon::parse($post->created_at)->longRelativeToNowDiffForHumans()?></small></p>
                </div>
                <div class="card-footer">
                    <div class="d-grid gap-2 d-flex justify-content-between">
                        <button type="button" class="btn btn-warning rounded-pill update-post-btn" data-id="<?=$post->id?>">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-danger rounded-pill remove-post-btn" data-id="<?=$post->id?>">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?endforeach;?>
    </div>
</div>

<div class="d-none" id="post_template">
    <div class="card h-100" id="post-#id">
        <div class="card-body">
            <h5 class="card-title">#title</h5>
            <p class="card-text">#text</p>
            <p class="card-text"><small class="text-body-secondary">#time</small></p>
        </div>
        <div class="card-footer">
            <div class="d-grid gap-2 d-flex justify-content-between">
                <button type="button" class="btn btn-warning rounded-pill update-post-btn" data-id="#id">
                    <i class="bi bi-pencil-square"></i>
                </button>
                <button type="button" class="btn btn-danger rounded-pill remove-post-btn" data-id="#id">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- TOASTS -->
<button type="button" class="btn btn-primary d-none" id="toastErrorBtn"></button>
<div class="toast-container position-fixed top-0 start-50 p-3">
    <div id="toastError" class="toast fade align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<button type="button" class="btn btn-primary d-none" id="toastSuccessBtn"></button>
<div class="toast-container position-fixed top-0 start-50 p-3">
    <div id="toastSuccess" class="toast fade align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<!-- /TOASTS -->

<!-- Modals -->
<div class="modal fade" id="addPost" aria-hidden="true" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5">Create Post</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form class="row g-3" method="post" action="posts/create.php" id="add_post_form">
                    <div class="form-floating mb-3">
                        <input type="text" name="title" class="form-control" id="addTitle" required>
                        <label for="addTitle">Title</label>
					</div>
                    <div class="form-floating mb-3">
                        <textarea name="text" class="form-control" id="addText" placeholder="" required></textarea>
                        <label for="addText">Text</label>
					</div>
					<div class="col-12 form-buttons">
						<button class="btn btn-primary w-100" type="submit">
                            <span class="old-label">Create</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span class="loading-label d-none">Loading...<span>
                        </button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<button type="button" class="btn btn-primary d-none" data-bs-toggle="modal" id="updatePostModalBtn" data-bs-target="#updatePost"></button>

<div class="modal fade" id="updatePost" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5" id="exampleModalToggleLabel">Edit Post</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form class="row g-3" novalidate method="post" action="posts/update.php" id="update_post_form">
					<input type="hidden" name="_method" value="put"/>
					<input type="hidden" name="id"/>
					<div class="form-floating mb-3">
                        <input type="text" name="title" class="form-control" id="updateTitle" required>
                        <label for="updateTitle" class="form-label">Title</label>
					</div>
					<div class="form-floating mb-3">
                        <textarea name="text" class="form-control" id="updateText" required></textarea>
                        <label for="updateText" class="form-label">Text</label>
					</div>
                    <div class="d-block d-sm-flex justify-content-sm-between gap-2">
                        <button class="btn btn-danger w-100 mb-2 mb-sm-0" type="button" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                        <button class="btn btn-primary w-100" type="submit">
                            <span class="old-label">Save</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span class="loading-label d-none">Loading...<span>
                        </button>
                    </div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- /Modals -->
<?php
require __DIR__ . '/../include/footer.php';
