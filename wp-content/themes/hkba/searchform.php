<form action="<?php echo home_url( '/' ); ?>" method="get">

	<div class="input-group add-on">
		<input type="text" name="s" id="search" placeholder="Search....." class="form-control" value="<?php the_search_query(); ?>" />
		<div class="input-group-btn">
			<button class="btn btn-default" id="searchsubmit" value="Search" type="submit"><i class="glyphicon glyphicon-search"></i></button>
		</div>
	</div>

</form>
