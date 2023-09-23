<?php 
    use App\Config;
?>

<?= Config::inc('backend.inc.header.header') ?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Brands</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Brands</a></li>
                    <li class="breadcrumb-item active">Add Brand</li>
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Add Brand</h4>

                <form action="" method="POST" id="myForm" enctype="multipart/form-data">
                    <div class="mb-3 row">
                        <label for="brand_name" class="col-md-2 col-form-label">name</label>
                        <div class="col-md-10">
                            <input name="name" class="form-control" type="text" placeholder="Brand Name" id="brand_name">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="brand_image" class="col-md-2 col-form-label">Brand Image</label>
                        <div class="col-md-10">
                            <input name="brand_image" class="form-control" type="file" id="brand_image">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="brand_image" class="col-md-2 col-form-label">Image Preview</label>
                        <div class="col-md-10">
                            <img src="" alt="" id="image_preview" width="200" class="rounded" >
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-md-2 col-form-label">Status</label>
                        <div class="col-md-10">
                            <select name="status" class="form-select">
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
    let src = null;
    const image_preview = document.getElementById('image_preview');
    const form = document.getElementById('myForm');


    form.addEventListener('submit',(e) => {
        e.preventDefault();
        const formData = new FormData(form);
        formData.append('brand_image',image_preview.src != window.location.href ? src : null)
        $.ajax({
            type: 'POST',
            url: '/admin/brand/create',
            processData: false,
            contentType: false,
            data: formData,
            success: function (msg) {
                const response = JSON.parse(msg);
                if(response.status === 200){
                    Swal.fire('Success',response.message,'success')
                    form.name.value = '';
                    form.status.value = 1;
                    image_preview.src = '';

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


    const brandImage = document.getElementById('brand_image')
    const preview = document.getElementById('image_preview')
    
    brandImage.addEventListener('change',function(e){
        e.preventDefault();
        src = e.target.files.length == 1 ? URL.createObjectURL(e.target.files[0]) : null
        preview.src = src
    })
</script>
<?= Config::inc('backend.inc.footer.footer') ?>