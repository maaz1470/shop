<?php 
    use App\Config;
?>

<?= Config::inc('backend.inc.header.header') ?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Category</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Category</a></li>
                    <li class="breadcrumb-item active">Add Category</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Add Category</h4>

                <form action="" method="POST" id="form" enctype="multipart/form-data">
                    <div class="mb-3 row">
                        <label for="category_name" class="col-md-2 col-form-label">Name</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" placeholder="Category Name" value="<?= $var['category']->name ?>" id="category_name">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="category_url" class="col-md-2 col-form-label">URL</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" placeholder="Category URL" value="<?= $var['category']->url ?>" id="category_url">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="description" class="col-md-2 col-form-label">Description</label>
                        <div class="col-md-10">
                            <textarea class="form-control" rows="10" placeholder="Description" id="description"><?= $var['category']->description ?></textarea>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-md-2 col-form-label">Status</label>
                        <div class="col-md-10">
                            <select class="form-select">
                                <option value="1">Published</option>
                                <option value="0">Unpublished</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-2">
                            <input id="submit" class="btn btn-success" type="submit" value="Submit" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div> <!-- end col -->
</div>
<script>
    // const submitBtn = document.getElementById('submit');
    const form = document.getElementById('form');
    form.addEventListener('submit',(e) => {
        e.preventDefault();
        const formData = new FormData(form);
        formData.append('name',form[0].value)
        formData.append('url',form[1].value)
        formData.append('description',form[2].value)
        formData.append('status',form[3].value)
        formData.append('id',<?= $var['category']->id ?>)
        $.ajax({
            type: 'POST',
            url: '/admin/category/updateCategory',
            processData: false,
            contentType: false,
            data: formData,
            success: function (msg) {
                const response = JSON.parse(msg);
                if(response.status === 200){
                    Swal.fire('Success',response.message,'success')
                }else if(response.status === 401){
                    Swal.fire('Error',response.message,'error')
                }else if(response.status === 403){
                    Swal.fire('Error',response.message,'error');
                }else{
                    Swal.fire('Error','Something went wrong. Please try again.','error');
                }
            }
        })
    })

    
    form[3].value = <?= $var['category']->status ?>
</script>
<?= Config::inc('backend.inc.footer.footer') ?>