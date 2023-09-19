<?php 
    use App\Config;
?>

<?= Config::inc('backend.inc.header.header') ?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Categories</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Category</a></li>
                    <li class="breadcrumb-item active">All Categories</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title">All Sub Categories</h4>
                    <a href="/admin/sub-category/add-subcategory" class="btn btn-success">Add Sub Category</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Prarent Category</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($var['subcategories'] as $index => $category): ?>
                                <tr>
                                    <th scope="row"><?= $index=$index+1 ?></th>
                                    <td><?= $category->name ?></td>
                                    <td><?= $category->category_name ?? 'Uncategorinized' ?></td>
                                    <td><?= $category->status === 1 ? "<button class='btn-sm btn btn-success'>Published</button>" : "<button class='btn-sm btn btn-danger'>Unpublished</button>" ?></td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <?php if($category->status == 1): ?>
                                            <button onclick="changeStatus(this,0,<?= $category->id ?>)" class="btn btn-success btn-sm mx-2"><i class="fa fa-arrow-circle-up" aria-hidden="true"></i></button>
                                            <?php else: ?>
                                            <button onclick="changeStatus(this,1,<?= $category->id ?>)" class="btn btn-danger btn-sm mx-2"><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></button>
                                            <?php endif; ?>
                                            <a href="/admin/sub-category/edit-subcategory/<?= $category->id ?>" class="btn btn-info btn-sm mx-2"><i class="fas fa-edit    "></i></a>
                                            <a href="javascript:void(0)" onclick="deleteCategory(this,<?= $category->id ?>)" class="btn btn-danger btn-sm mx-2"><i class="fas fa-trash    "></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    
</div>

<script>
    function changeStatus(e,status,id){
        $.ajax({
            url: '/admin/sub-category/statusChange',
            type: 'POST',
            data: {
                status: status,
                id: id
            },
            success: function(msg){
                const response = JSON.parse(msg)
                if(response.status === 200){
                    Swal.fire('Success',response.message,'success')
                    setTimeout(() => {
                        window.location.reload();
                    },1000)
                }else if(response.status === 401){
                    Swal.fire('Error',response.message,'error')
                }else if(response.status === 403){
                    Swal.fire('Error',response.message,'error');
                }else{
                    Swal.fire('Error','Something went wrong. Please try again.','error');
                }
            }
        })
    }

    function deleteCategory(e, id){
        Swal.fire(
            {
                title: 'Are you Sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }
        ).then((result) => {
            if(result.isConfirmed){
                $.ajax({
                type: 'POST',
                url:'/admin/sub-category/delete',
                data: {
                    id: id
                },
                success: function (msg){
                    const response = JSON.parse(msg)
                    if(response.status === 200){
                        e.closest('tr').remove()
                        Swal.fire('Success',response.message,'success')
                    }else{
                        Swal.fire('Error','Something went wrong. Please try again or contact your developer.')
                    }
                }
            })
            }
            
        })
    }
</script>


<?= Config::inc('backend.inc.footer.footer') ?>