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
                    <li class="breadcrumb-item active">Add Sub Category</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Add Sub Category</h4>

                <form action="" method="POST" id="form" enctype="multipart/form-data">
                    <div class="mb-3 row">
                        <label for="category_name" class="col-md-2 col-form-label">name</label>
                        <div class="col-md-10">
                            <input class="form-control" type="text" placeholder="Category Name" id="category_name">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="parent_category" class="col-md-2 col-form-label">Parent Category</label>
                        <div class="col-md-10">
                            <select name="parent_category" class="form-control" id="parent_category">
                                <?php foreach($var['parents'] as $category): ?>
                                    <option value="<?= $category->id ?>"><?= $category->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="description" class="col-md-2 col-form-label">Description</label>
                        <div class="col-md-10">
                            <textarea class="form-control" rows="10" placeholder="Description" id="description"></textarea>
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
        formData.append('parent_id',form[1].value)
        formData.append('description',form[2].value)
        formData.append('status',form[3].value)
        $.ajax({
            type: 'POST',
            url: '/admin/sub-sub-category/submitSubCategory',
            processData: false,
            contentType: false,
            data: formData,
            success: function (msg) {
                const response = JSON.parse(msg);
                if(response.status === 200){
                    Swal.fire('Success',response.message,'success')
                    form[0].value = '';
                    form[1].value = 0;
                    form[2].value = '';
                    form[3].value= 1;
                }else if(response.status === 401){
                    Swal.fire('Error',response.message,'error')
                }else if(response.status === 403){
                    Swal.fire('Error',response.message,'error');
                }else{
                    Swal.fire('Error','Something went wrong. Please try again.','error');
                }
                // console.log(msg)
            }
        })
    })
</script>
<?= Config::inc('backend.inc.footer.footer') ?>