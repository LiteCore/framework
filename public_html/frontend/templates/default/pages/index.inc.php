{{notices}}

<main id="content" class="container">

	<div class="card">
	
		<div class="card-header">
			<div class="card-title">
				<?php echo PLATFORM_NAME; ?>/<?php echo PLATFORM_VERSION; ?>
			</div>
		</div>
	
		<div class="card-body">
			<section class="twelve-eighty">
				<h1>CSS Framework</h1>
			</section>
	
			<section class="twelve-eighty">
	
				<div class="navbar">
	
					<div class="navbar-header">
						<div class="navbar-brand">
							Navbar
						</div>
	
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#example-menu">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
					</div>
	
					<nav id="example-menu" class="navbar-collapse collapse">
	
						<ul class="nav navbar-nav">
							<li>
								<a href="#">Single Item</a>
							</li>
	
							<li class="dropdown">
								<a href="#" data-toggle="dropdown" class="dropdown-toggle">Dropdown <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="#">Item 1</a></li>
									<li><a href="#">Item 2</a></li>
									<li><a href="#">Item 3</a></li>
								</ul>
							</li>
						</ul>
	
						<ul class="nav navbar-nav navbar-right">
							<li><a href="#">Single Item</a></li>
						</ul>
					</nav>
	
				</div>
			</section>
	
			<section class="twelve-eighty">
	
				<h2>Tabs</h2>
	
				<ul class="nav nav-tabs">
					<li><a href="#tab-1" data-toggle="tab">Tab 1</a></li>
					<li><a href="#tab-2" data-toggle="tab">Tab 2</a></li>
				</ul>
	
				<div class="tab-content">
					<div id="tab-1" class="tab-pane">
						Content 1
					</div>
	
					<div id="tab-2" class="tab-pane">
						Content 2
					</div>
				</div>
			</section>
	
			<section class="twelve-eighty">
	
				<h2>Pills</h2>
	
				<ul class="nav nav-pills">
					<li class="active"><a href="#">Home</a></li>
					<li><a href="#">Profile</a></li>
					<li><a href="#">Messages</a></li>
				</ul>
	
				<h2>Stacked Pills</h2>
	
				<ul class="nav nav-pills nav-stacked">
					<li class="active"><a href="#">Home</a></li>
					<li><a href="#">Profile</a></li>
					<li><a href="#">Messages</a></li>
				</ul>
			</section>
	
			<section class="twelve-eighty">
	
				<h2>Notices</h2>
	
				<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a><i class="fa fa-exclamation-triangle"></i> Lorem ipsum dolor</div>
				<div class="alert alert-warning"><a href="#" class="close" data-dismiss="alert">&times;</a><i class="fa fa-exclamation-triangle"></i> Lorem ipsum dolor</div>
				<div class="alert alert-default"><a href="#" class="close" data-dismiss="alert">&times;</a><i class="fa fa-info-circle"></i> Lorem ipsum dolor</div>
				<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert">&times;</a><i class="fa fa-check-circle"></i> Lorem ipsum dolor</div>
			</section>
	
			<section class="twelve-eighty">
	
				<h2>Breadcrumbs</h2>
	
				<ul class="breadcrumb">
					<li><a href="#">Home</a></li>
					<li>Page</li>
				</ul>
	
			</section>
	
			<section class="twelve-eighty">
	
				<h2>Pagination</h2>
	
				<nav>
					<ul class="pagination">
						<li class="disabled"><span>&laquo;</span></li>
						<li class="active"><span>1</span></li>
						<li><a href="#">2</a></li>
						<li><a href="#">3</a></li>
						<li><a href="#">4</a></li>
						<li><a href="#">5</a></li>
						<li><a href="#"><span>&raquo;</span></a>
						</li>
					</ul>
				</nav>
			</section>
	
			<section class="twelve-eighty">
	
				<h2>Carousel</h2>
	
				<div id="carousel-example" class="carousel slide" data-ride="carousel">
	
					<div class="carousel-inner">
						<div class="item active">
							<img src="https://via.placeholder.com/1280x480.png?text=First%20slide" />
						</div>
	
						<div class="item">
							<img src="https://via.placeholder.com/1280x480.png?text=Second%20slide" />
							<div class="carousel-caption">Lorem ipsum</div>
						</div>
	
						<div class="item">
							<img src="https://via.placeholder.com/1280x480.png?text=Third%20slide" />
							<div class="carousel-caption">Dolor sit amet</div>
						</div>
					</div>
	
					<ol class="carousel-indicators">
						<li data-target="#carousel-example" data-slide-to="1" class="active"></li>
						<li data-target="#carousel-example" data-slide-to="2"></li>
						<li data-target="#carousel-example" data-slide-to="3"></li>
					</ol>
	
					<a class="carousel-control left" href="#carousel-example" role="button" data-slide="prev">
						<span class="icon-next"><i class="fa fa-chevron-left"></i></span>
					</a>
					<a class="carousel-control right" href="#carousel-example" role="button" data-slide="next">
						<span class="icon-next"><i class="fa fa-chevron-right"></i></span>
					</a>
				</div>
			</section>
	
			<section class="twelve-eighty">
	
				<h2>Panels</h2>
	
				<section id="panel-example" class="panel panel-default">
					<div class="panel-heading">
						<h1 class="panel-title">Default Panel</h1>
					</div>
	
					<div class="panel-body">
						Body
					</div>
	
					<div class="panel-footer">
						Footer
					</div>
				</section>
	
				<section id="panel-example" class="panel panel-primary">
					<div class="panel-heading">
						<h1 class="panel-title">Primary Panel</h1>
					</div>
	
					<div class="panel-body">
						Body
					</div>
	
					<div class="panel-footer">
						Footer
					</div>
				</section>
	
				<section id="panel-example" class="panel panel-info">
					<div class="panel-heading">
						<h1 class="panel-title">Info Panel</h1>
					</div>
	
					<div class="panel-body">
						Body
					</div>
	
					<div class="panel-footer">
						Footer
					</div>
				</section>
			</section>
	
			<section class="twelve-eighty">
	
				<h2>Grid</h2>
	
				<div class="row show-grid">
					<div class="col-md-1">.col-md-1</div>
					<div class="col-md-1">.col-md-1</div>
					<div class="col-md-1">.col-md-1</div>
					<div class="col-md-1">.col-md-1</div>
					<div class="col-md-1">.col-md-1</div>
					<div class="col-md-1">.col-md-1</div>
					<div class="col-md-1">.col-md-1</div>
					<div class="col-md-1">.col-md-1</div>
					<div class="col-md-1">.col-md-1</div>
					<div class="col-md-1">.col-md-1</div>
					<div class="col-md-1">.col-md-1</div>
					<div class="col-md-1">.col-md-1</div>
				</div>
	
				<div class="row show-grid">
					<div class="col-md-8">.col-md-8</div>
					<div class="col-md-4">.col-md-4</div>
				</div>
	
				<div class="row show-grid">
					<div class="col-md-4">.col-md-4</div>
					<div class="col-md-4">.col-md-4</div>
					<div class="col-md-4">.col-md-4</div>
				</div>
	
				<div class="row show-grid">
					<div class="col-md-6">.col-md-6</div>
					<div class="col-md-6">.col-md-6</div>
				</div>
			</section>
	
			<section class="twelve-eighty">
	
				<div class="row">
					<div class="col-md-4">
						<h1>Heading 1</h1>
					</div>
	
					<div class="col-md-4">
						<h2>Heading 2</h2>
					</div>
	
					<div class="col-md-4">
						<h3>Heading 3</h3>
					</div>
				</div>
	
				<h2>Text</h2>
	
				<p><strong>Lorem ipsum dolor sit amet</strong>, <u>consectetur adipiscing elit</u>. <i>Phasellus dignissim sollicitudin orci</i>, sed semper purus sodales et. Aenean a lorem vestibulum ipsum feugiat congue. Nullam nec turpis at augue tincidunt lobortis. Etiam pulvinar neque sed mi accumsan, nec gravida eros dignissim. Nunc ultricies urna ac eros tempor porta. Fusce maximus nec magna nec dictum. Donec at nibh feugiat, porta ante eu, aliquet ligula. Mauris feugiat lectus ut vulputate malesuada. Phasellus aliquet molestie odio, sed eleifend ante lacinia vel. Etiam mattis neque sit amet suscipit dictum. Sed blandit consectetur dolor, et accumsan purus gravida ut. Pellentesque porttitor pharetra nisl, sit amet dignissim arcu mollis non. Sed eu blandit metus, non bibendum nulla. Praesent pharetra orci eget ipsum lacinia pretium. Praesent purus sem, volutpat id lorem quis, placerat malesuada magna. Aliquam et nisl nec eros viverra consequat eget eu urna.</p>
	
				<h3>Subtitle</h3>
	
				<p>Integer in felis id dolor malesuada laoreet ut vitae metus. Nam ligula justo, posuere et gravida eu, faucibus nec dolor. Vestibulum tincidunt felis a commodo faucibus. Donec et elit molestie, convallis felis at, viverra purus. Pellentesque venenatis, leo et scelerisque semper, arcu nisi sagittis tortor, eget accumsan lacus nisl a felis. Sed hendrerit suscipit semper. Vestibulum a mi id mauris varius tincidunt. Integer sed purus nec sem convallis commodo. Praesent sagittis sagittis accumsan. Nulla facilisi. Nunc auctor commodo faucibus.</p>
	
				<small>Pellentesque egestas felis sed dignissim ullamcorper.</small>
			</section>
	
			<section class="twelve-eighty">
				<h3>Unstyled List</h3>
	
				<ul class="list-unstyled">
					<li>Item 1</li>
					<li>Item 2</li>
					<li>Item 3</li>
				</ul>
	
				<h3>Inline List</h3>
	
				<ul class="list-inline">
					<li>Item 1</li>
					<li>Item 2</li>
					<li>Item 3</li>
				</ul>
			</section>
	
			<section class="twelve-eighty">
				<h2>Title</h2>
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th>#</th>
							<th>First Name</th>
							<th>Last Name</th>
							<th>Username</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th scope="row">1</th>
							<td>Mark</td>
							<td>Otto</td>
							<td>@mdo</td>
						</tr>
						<tr>
							<th scope="row">2</th>
							<td>Jacob</td>
							<td>Thornton</td>
							<td>@fat</td>
						</tr>
						<tr>
							<th scope="row">3</th>
							<td>Larry</td>
							<td>the Bird</td>
							<td>@twitter</td>
						</tr>
					</tbody>
				</table>
			</section>
	
			<section class="twelve-eighty">
	
				<h2>Forms</h2>
	
				<div class="row">
					<div class="form-group col-md-3">
						<label>Text</label>
						<input type="text" class="form-input" placeholder="Text" />
					</div>
	
					<div class="form-group col-md-3">
						<label>File</label>
						<input type="file" class="form-input" />
					</div>
	
					<div class="form-group col-md-3">
						<label>Color</label>
						<input type="color" class="form-input" placeholder="#fff" />
					</div>
	
					<div class="form-group col-md-3">
						<label>Input Group</label>
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-envelope-o"></i></span>
							<input type="email" class="form-input" placeholder="Email" />
						</div>
					</div>
	
					<div class="form-group col-md-3">
						<label>Input Group With Button</label>
						<div class="input-group">
							<input type="text" class="form-input" placeholder="Text" />
							<span class="input-group-btn">
								<button class="btn btn-default">Button</button>
							</span>
						</div>
					</div>
	
					<div class="form-group col-md-3">
						<label>Select</label>
						<select class="form-input">
							<option>-- Select --</option>
							<option>A</option>
							<option>B</option>
							<option>C</option>
						</select>
					</div>
	
					<div class="form-group col-md-3">
						<label>Checkbox</label>
						<div class="checkbox">
							<input type="checkbox" checked="checked" /> Value
						</div>
					</div>
	
					<div class="form-group col-md-3">
						<label>Radio</label>
						<div class="checkbox">
							<input type="radio" checked="checked" /> Value
						</div>
					</div>
				</div>
	
				<div class="form-group">
					<label>Textarea</label>
					<textarea class="form-input" placeholder="Lorem ipsum dolor..."></textarea>
				</div>
	
			</section>
	
			<section class="twelve-eighty">
	
				<h2>Buttons</h2>
	
				<h3>Small</h3>
	
				<div class="row">
					<div class="col-md-6">
						<button type="button" class="btn btn-sm btn-default">Default</button>
						<button type="button" class="btn btn-sm btn-primary">Primary</button>
						<button type="button" class="btn btn-sm btn-success">Success</button>
						<button type="button" class="btn btn-sm btn-danger">Danger</button>
					</div>
	
					<div class="col-md-3">
						<button type="button" class="btn btn-sm btn-default btn-block">Default</button>
					</div>
	
					<div class="col-md-3">
						<div class="btn-group btn-block btn-group-inline" data-toggle="buttons">
							<label class="btn btn-sm btn-default active"><input type="radio"  value="1" checked="checked"> Yes</label>
							<label class="btn btn-sm btn-default"><input type="radio" value="0"> No</label>
						</div>
					</div>
				</div>
	
				<h3>Normal</h3>
	
				<div class="row">
					<div class="col-md-6">
						<button type="button" class="btn btn-default">Default</button>
						<button type="button" class="btn btn-primary">Primary</button>
						<button type="button" class="btn btn-success">Success</button>
						<button type="button" class="btn btn-danger">Danger</button>
					</div>
	
					<div class="col-md-3">
						<button type="button" class="btn btn-default btn-block">Default</button>
					</div>
	
					<div class="col-md-3">
						<div class="btn-group btn-block btn-group-inline" data-toggle="buttons">
							<label class="btn btn-default active"><input type="radio" value="1" checked="checked"> Yes</label>
							<label class="btn btn-default"><input type="radio" value="0"> No</label>
						</div>
					</div>
				</div>
	
				<h3>Large</h3>
	
				<div class="row">
					<div class="col-md-6">
						<button type="button" class="btn btn-lg btn-default">Default</button>
						<button type="button" class="btn btn-lg btn-primary">Primary</button>
						<button type="button" class="btn btn-lg btn-success">Success</button>
						<button type="button" class="btn btn-lg btn-danger">Danger</button>
					</div>
	
					<div class="col-md-3">
						<button type="button" class="btn btn-lg btn-default btn-block">Default</button>
					</div>
	
					<div class="col-md-3">
						<div class="btn-group btn-block btn-group-inline" data-toggle="buttons">
							<label class="btn btn-lg btn-default active"><input type="radio"  value="1" checked="checked"> Yes</label>
							<label class="btn btn-lg btn-default"><input type="radio" value="0"> No</label>
						</div>
					</div>
				</div>
	
				<h3>Button Group</h3>
	
				<div class="btn-group">
					<button type="button" class="btn btn btn-default">Default</button>
					<button type="button" class="btn btn btn-primary">Primary</button>
					<button type="button" class="btn btn btn-success">Success</button>
					<button type="button" class="btn btn btn-danger">Danger</button>
				</div>
	
				<h3>Button Dropdown</h3>
	
				<div class="btn-group">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						Action <span class="caret"></span>
					</button>
	
					<ul class="dropdown-menu">
						<li><a href="#">Action</a></li>
						<li><a href="#">Another action</a></li>
						<li><a href="#">Something else here</a></li>
						<li class="divider"></li>
						<li><a href="#">Separated link</a></li>
					</ul>
				</div>
	
			</section>
	
			<section class="twelve-eighty">
				<h3>Thumbnails</h3>
	
				<div class="row">
					<div class="col-md-3">
						<a href="#" class="thumbnail">
							<img src="https://via.placeholder.com/300x200.png" />
						</a>
					</div>
	
					<div class="col-md-3">
						<a href="#" class="thumbnail">
							<img src="https://via.placeholder.com/300x200.png" />
						</a>
					</div>
	
					<div class="col-md-3">
						<a href="#" class="thumbnail">
							<img src="https://via.placeholder.com/300x200.png" />
						</a>
					</div>
	
					<div class="col-md-3">
						<a href="#" class="thumbnail">
							<img src="https://via.placeholder.com/300x200.png" />
						</a>
					</div>
				</div>
			</section>
	
			<section class="twelve-eighty">
				<h3>Loader</h3>
	
				<div class="loader" style="width: 128px; height: 128px;"></div>
			</section>
	
		</div>
	</div>
</main>