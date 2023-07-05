<?php
?>
<div class="col-lg-12">
	<div class="card">
	<div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="text-center" style="font-weight: bold; font-size: 24px;"> Register New Air Base</h1>
            </div>
     </div>
		<div class="card-body">
			<form action="" id="manage_user">
				<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
				<div class="row">
					<div class="col-md-6 border-right">
						<div class="form-group">
							<label for="" class="control-label">Base Name</label>
							<input type="text" name="name" class="form-control form-control-sm" required value="">
						</div>
						<div class="form-group">
							<label for="" class="control-label">Base Location</label>
							<input type="text" name="location" class="form-control form-control-sm" required value="">
						</div>
					</div>
				<hr>
				<div class="col-lg-12 text-right justify-content-center d-flex">
					<button class="btn btn-primary mr-2">Save</button>
					<button class="btn btn-secondary" type="button" onclick="location.href = 'index.php?page=bases'">Cancel</button>
				</div>
			</form>
		</div>
	</div>
</div>
<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
		<div class="d-flex justify-content-between align-items-center">
            <h1 class="text-center" style="font-weight: bold; font-size: 24px;">Air Bases List</h1>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
			<input type="text" id="search-input" class="form-control mb-3" placeholder="Search">
            <table class="table table-hover table-condensed" id="list">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Name</th>
						<th>Location</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$qry = $conn->query("SELECT * FROM bases order by id asc");
					while($row= $qry->fetch_assoc()):
					?>
					<tr>
						<th class="text-center"><?php echo $row['id'] ?></th>
						<td><b><?php echo ucwords($row['name']) ?></b></td>
						<td><b><?php echo $row['location'] ?></b></td>
						<td>
							<button class="btn btn-sm btn-danger delete_btn" data-id="<?php echo $row['id'] ?>">Delete</button>
						</td> <!-- Added delete button -->
					</tr>	
				<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
	$(document).on('click', '.delete_btn', function(e){
		e.preventDefault();
		var baseId = $(this).data('id');

			$.ajax({
				url: 'ajax.php?action=delete_base',
				method: 'POST',
				data: { base_id: baseId },
				success: function(resp){
					console.log(resp);
					if(resp == 1){
						alert_toast('Data successfully deleted.', "success");
						setTimeout(function(){
							location.reload();
						}, 750);
					}else{
						alert_toast('An error occurred:'.resp, "error");
					}
				},
				error: function(jqXHR, textStatus, errorThrown){
					console.log(textStatus, errorThrown);
				}
			});
	});

	$('#manage_user').submit(function(e){
		e.preventDefault()
		$('input').removeClass("border-danger")
		start_load()
		$('#msg').html('')
		$.ajax({
			url:'ajax.php?action=save_base',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				console.log(resp);
				if(resp == 1){
					alert_toast('Data successfully saved.',"success");
					setTimeout(function(){
						location.replace('index.php?page=bases')
					},750)
				}else if(resp == 2){
					$('#msg').html("<div class='alert alert-danger'>base already exist.</div>");
					end_load()
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(textStatus, errorThrown);
				end_load()
			}
		})
	})
	 // Filter table rows based on search term
	 $("#search-input").on("keyup", function() {
        var searchTerm = $(this).val().toLowerCase();
        $("#list tbody tr").each(function() {
            var $row = $(this);
            var rowData = $row.text().toLowerCase();
            if (rowData.indexOf(searchTerm) === -1) {
                $row.hide();
            } else {
                $row.show();
            }
        });
    });
</script>