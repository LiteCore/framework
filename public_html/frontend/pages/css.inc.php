<?php

	/*!
	 * If you would like to maintain visual changes in a separate file, create the following template file for your HTML:
	 *
	 *   ~/frontend/template/pages/css.inc.php
	 */

	document::$title = [language::translate('index:head_title', ''), settings::get('site_name')];
	document::$description = language::translate('index:meta_description', '');
	document::$opengraph['url'] = document::href_ilink('');
	document::$opengraph['type'] = 'website';
	document::$opengraph['image'] = document::href_rlink('storage://images/logotype.png');

	$_page = new ent_view('app://frontend/template/pages/css.inc.php');

	// Place your snippets here
	// ...

	if (is_file($_page->view)) {
		echo $_page->render();
		return;
	} else {
		extract($_page->snippets);
	}

?>
<style>
.code {
	font-family: monospace;
	white-space: break-spaces;
	overflow-x: auto;
	background: #345;
	color: #fff;
	font-size: .8em;
	padding: 1em;
	border-radius: var(--border-radius);
	tab-size: 2;
}
.show-grid [class*="col"] {
	border: 1px dotted #ccc;
}
</style>

{{notices}}

<main id="content" class="container">

	<h1>
		CSS Framework
	</h1>

	<section class="card">

		<div class="card-header">
			<div class="card-title">
				Navgation Bar
			</div>
		</div>

		<div class="card-body">

			<div class="row">
				<div class="col-md-6 source">
<div class="navbar">

	<div class="navbar-header">
		<div class="navbar-brand">
			Navbar
		</div>

		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#code-menu">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
	</div>

	<nav class="navbar-collapse collapse">

		<ul class="nav navbar-nav">
			<li>
				<a href="#">Single Item</a>
			</li>

			<li class="dropdown">
				<a href="#" data-toggle="dropdown" class="dropdown-toggle">
					Dropdown <b class="caret"></b>
				</a>
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
				</div>

				<div class="col-md-6 code">
		</div>

	</section>

	<section class="card">

		<div class="card-header">
			<div class="card-title">
				Cards
			</div>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-md-6 source">
<section class="card">
	<div class="card-header">
		<div class="card-title">
			Card Title
		</div>
	</div>

	<div class="card-body">
		Card Body
	</div>

	<div class="card-footer">
		Card Footer
	</div>
</section>

<section class="card">
	<div class="card-header">
		<div class="card-title">
		Card Title
		</div>
	</div>

	<div class="card-body">
		Card Body
	</div>
</section>

<section class="card">
	<div class="card-body">
		Card Body
	</div>
</section>
				</div>

				<div class="col-md-6 code">
				</div>
			</div>
		</div>
	</section>

	<section class="card">

		<div class="card-header">
			<div class="card-title">
			Tabs
			</div>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-md-6 source">
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
				</div>

				<div class="col-md-6 code">
				</div>
			</div>
		</div>
	</section>

	<section class="card">

		<div class="card-header">
			<div class="card-title">
				Pills
			</div>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-md-6 source">
<ul class="nav nav-pills">
	<li class="active"><a href="#">Home</a></li>
	<li><a href="#">Profile</a></li>
	<li><a href="#">Messages</a></li>
</ul>
				</div>

				<div class="col-md-6 code">
				</div>
			</div>

			<h2>Stacked Pills</h2>

			<div class="row">
				<div class="col-md-6 source">
<ul class="nav nav-pills nav-stacked">
	<li class="active"><a href="#">Home</a></li>
	<li><a href="#">Profile</a></li>
	<li><a href="#">Messages</a></li>
</ul>
				</div>

				<div class="col-md-6 code">
				</div>
			</div>
		</div>
	</section>

	<section class="card">

		<div class="card-header">
			<div class="card-title">
			 Notices
			</div>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-md-6 source">
<div class="alerts">
	<div class="alert alert-danger">
		<i class="fa icon-exclamation-triangle"></i> Lorem ipsum dolor
		<a href="#" class="close" data-dismiss="alert">&times;</a>
	</div>

	<div class="alert alert-warning">
		<i class="fa icon-exclamation-triangle"></i> Lorem ipsum dolor
		<a href="#" class="close" data-dismiss="alert">&times;</a>
	</div>

	<div class="alert alert-default">
		<i class="fa icon-info-circle"></i> Lorem ipsum dolor
		<a href="#" class="close" data-dismiss="alert">&times;</a>
	</div>

	<div class="alert alert-success">
		<i class="fa icon-check-circle"></i> Lorem ipsum dolor
		<a href="#" class="close" data-dismiss="alert">&times;</a>
	</div>
</div>
				</div>

				<div class="col-md-6 code">
				</div>
			</div>
		</div>
	</section>

	<section class="card">

		<div class="card-header">
			<div class="card-title">
				Breadcrumbs
			</div>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-md-6 source">
<ul class="breadcrumb">
	<li><a href="#">Home</a></li>
	<li>Page</li>
</ul>
				</div>

				<div class="col-md-6 code">
				</div>
			</div>
		</div>
	</section>

	<section class="card">

		<div class="card-header">
			<div class="card-title">
				Pagination
			</div>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-md-6 source">
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
				</div>

				<div class="col-md-6 code">
				</div>
			</div>
		</div>
	</section>

	<section class="card">

		<div class="card-header">
			<div class="card-title">
				Carousel
			</div>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-md-6 source">
<div id="carousel-code" class="carousel slide" data-ride="carousel">

	<div class="carousel-inner">
		<div class="item active">
			<img src="https://via.placeholder.com/1280x480.png?text=First%20slide" loading="lazy">
		</div>

		<div class="item">
			<img src="https://via.placeholder.com/1280x480.png?text=Second%20slide" loading="lazy">
			<div class="carousel-caption">Lorem ipsum</div>
		</div>

		<div class="item">
			<img src="https://via.placeholder.com/1280x480.png?text=Third%20slide" loading="lazy">
			<div class="carousel-caption">Dolor sit amet</div>
		</div>
	</div>

	<ol class="carousel-indicators">
		<li data-target="#carousel-code" data-slide-to="1" class="active"></li>
		<li data-target="#carousel-code" data-slide-to="2"></li>
		<li data-target="#carousel-code" data-slide-to="3"></li>
	</ol>

	<a class="carousel-control left" href="#carousel-code" role="button" data-slide="prev">
		<span class="icon-next"><i class="fa icon-chevron-left"></i></span>
	</a>
	<a class="carousel-control right" href="#carousel-code" role="button" data-slide="next">
		<span class="icon-next"><i class="fa icon-chevron-right"></i></span>
	</a>
</div>
				</div>

				<div class="col-md-6 code">
				</div>
			</div>
		</div>
	</section>

	<section class="card">

		<div class="card-header">
			<div class="card-title">
				Grid
			</div>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-md-6 source">
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
				</div>

				<div class="col-md-6 code">
				</div>
			</div>
		</div>
	</section>

	<section class="card">

		<div class="card-header">
			<div class="card-title">

			</div>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-md-6 source">
<h1>Heading 1</h1>

<p><strong>Lorem ipsum dolor sit amet</strong>, <u>consectetur adipiscing elit</u>. <i>Phasellus dignissim sollicitudin orci</i>, sed semper purus sodales et. Aenean a lorem vestibulum ipsum feugiat congue. Nullam nec turpis at augue tincidunt lobortis. Etiam pulvinar neque sed mi accumsan, nec gravida eros dignissim. Nunc ultricies urna ac eros tempor porta. Fusce maximus nec magna nec dictum. Donec at nibh feugiat, porta ante eu, aliquet ligula. Mauris feugiat lectus ut vulputate malesuada. Phasellus aliquet molestie odio, sed eleifend ante lacinia vel. Etiam mattis neque sit amet suscipit dictum. Sed blandit consectetur dolor, et accumsan purus gravida ut. Pellentesque porttitor pharetra nisl, sit amet dignissim arcu mollis non. Sed eu blandit metus, non bibendum nulla. Praesent pharetra orci eget ipsum lacinia pretium. Praesent purus sem, volutpat id lorem quis, placerat malesuada magna. Aliquam et nisl nec eros viverra consequat eget eu urna.</p>

<h2>Heading 2</h2>

<p>Integer in felis id dolor malesuada laoreet ut vitae metus. Nam ligula justo, posuere et gravida eu, faucibus nec dolor. Vestibulum tincidunt felis a commodo faucibus. Donec et elit molestie, convallis felis at, viverra purus. Pellentesque venenatis, leo et scelerisque semper, arcu nisi sagittis tortor, eget accumsan lacus nisl a felis. Sed hendrerit suscipit semper. Vestibulum a mi id mauris varius tincidunt. Integer sed purus nec sem convallis commodo. Praesent sagittis sagittis accumsan. Nulla facilisi. Nunc auctor commodo faucibus.</p>

<small>Pellentesque egestas felis sed dignissim ullamcorper.</small>
				</div>

				<div class="col-md-6 code">
				</div>
			</div>
		</div>
	</section>

	<section class="card">

		<div class="card-header">
			<div class="card-title">
				Unstyled List
			</div>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-md-6 source">
<ul class="flex flex-rows">
	<li>Item 1</li>
	<li>Item 2</li>
	<li>Item 3</li>
</ul>
				</div>

				<div class="col-md-6 code">
				</div>
			</div>

			<h3>Inline List</h3>

			<div class="row">
				<div class="col-md-6 source">
<ul class="flex flex-columns flex-gap">
	<li>Item 1</li>
	<li>Item 2</li>
	<li>Item 3</li>
</ul>
				</div>

				<div class="col-md-6 code">
				</div>
			</div>
		</div>
	</section>

	<section class="card">

		<div class="card-header">
			<div class="card-title">
				Table
			</div>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-md-6 source">
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
				</div>

				<div class="col-md-6 code">
				</div>
			</div>
		</div>

	</section>

	<section class="card">

		<div class="card-header">
			<div class="card-title">
				Form Elements
			</div>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-md-6 source">
<div class="form-group">
	<label>Text</label>
	<input type="text" class="form-input" placeholder="Text">
</div>

<div class="form-group">
	<label>File</label>
	<input type="file" class="form-input">
</div>

<div class="form-group">
	<label>Color</label>
	<input type="color" class="form-input" placeholder="#fff">
</div>

<div class="form-group">
	<label>Input Group Icon</label>
	<div class="input-group">
		<span class="input-group-icon"><i class="fa icon-envelope-o"></i></span>
		<input type="email" class="form-input" placeholder="Email">
	</div>
</div>

<div class="form-group">
	<label>Input Group Text</label>
	<div class="input-group">
		<span class="input-group-text">Text</span>
		<input type="email" class="form-input" placeholder="Email">
	</div>
</div>

<div class="form-group">
	<label>Input Group Button</label>
	<div class="input-group">
		<input type="text" class="form-input" placeholder="Text">
		<button class="btn btn-default">Button</button>
	</div>
</div>

<div class="form-group">
	<label>Select</label>
	<select class="form-select">
		<option>-- Select --</option>
		<option>A</option>
		<option>B</option>
		<option>C</option>
	</select>
</div>

<div class="form-group">
	<label>Select Dropdown</label>
	<div class="dropdown">
		<div class="form-select" data-toggle="dropdown">
			-- Select --
		</div>
		<ul class="dropdown-menu">
			<li class="option"><label class="checkbox"><input type="checkbox"> Value</label></li>
			<li class="option"><label class="checkbox"><input type="checkbox"> Value</label></li>
		</ul>
	</div>
</div>

<div class="form-group">
	<label>Checkbox</label>
	<label class="checkbox">
		<input type="checkbox" name="checkbox"> Value
	</label>
	<label class="checkbox">
		<input type="checkbox" name="checkbox" checked> Value
	</label>
</div>

<div class="form-group">
	<label>Radio</label>
	<label class="checkbox">
		<input type="radio" name="radio"> Value
	</label>
	<label class="checkbox">
		<input type="radio" name="radio" checked> Value
	</label>
</div>

<div class="form-group">
	<label>Textarea</label>
	<textarea class="form-input" placeholder="Lorem ipsum dolor..."></textarea>
</div>

<fieldset>
	<legend>Fieldset</legend>

	<div class="form-group">
		<label>Label</label>
		<input type="text" class="form-input" placeholder="Lorem ipsum dolor...">
	</div>
</fieldset>

<div class="form-group">
	<label>Toggle</label>
	<?php echo functions::form_toggle('toggle', [['one', 'One'], ['two', 'Two'], ['three', 'Three']], 'one'); ?>
</div>

<div class="form-group">
	<label>Toggle</label>
	<?php echo functions::form_toggle('toggle[]', [['one', 'One'], ['two', 'Two'], ['three', 'Three']], ['one', 'two']); ?>
</div>

<div class="form-group">
	<label>Switch</label>
	<?php echo functions::form_switch('switch', '1'); ?>
</div>

<div class="form-group">
	<label>Tags</label>
	<?php echo functions::form_input_tags('tags[]'); ?>
</div>
				</div>

				<div class="col-md-6 code">
				</div>
			</div>
		</div>
	</section>

	<section class="card">

		<div class="card-header">
			<div class="card-title">
				Buttons
			</div>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-md-6 source">
<button type="button" class="btn btn-sm btn-default">Default</button>
<button type="button" class="btn btn-sm btn-primary">Primary</button>
<button type="button" class="btn btn-sm btn-success">Success</button>
<button type="button" class="btn btn-sm btn-danger">Danger</button>

<br>
<br>

<button type="button" class="btn btn-default">Default</button>
<button type="button" class="btn btn-primary">Primary</button>
<button type="button" class="btn btn-success">Success</button>
<button type="button" class="btn btn-danger">Danger</button>

<br>
<br>

<button type="button" class="btn btn-lg btn-default">Default</button>
<button type="button" class="btn btn-lg btn-primary">Primary</button>
<button type="button" class="btn btn-lg btn-success">Success</button>
<button type="button" class="btn btn-lg btn-danger">Danger</button>
				</div>

				<div class="col-md-6 code">
				</div>
			</div>

			<h3>Button Group</h3>

			<div class="row">
				<div class="col-md-6 source">
<div class="btn-group">
	<button type="button" class="btn btn-default">Button 1</button>
	<button type="button" class="btn btn-default">Button 2</button>
	<button type="button" class="btn btn-default">Button 3</button>
	<button type="button" class="btn btn-default">Button 4</button>
</div>

<br>
<br>

<div class="btn-group btn-block">
	<button type="button" class="btn btn-default">Button 1</button>
	<button type="button" class="btn btn-default">Button 2</button>
	<button type="button" class="btn btn-default">Button 3</button>
	<button type="button" class="btn btn-default">Button 4</button>
</div>
				</div>

				<div class="col-md-6 code">
				</div>
			</div>

			<h3>Button Dropdown</h3>

			<div class="row">
				<div class="col-md-6 source">
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
				</div>

				<div class="col-md-6 code">
				</div>
			</div>
		</div>
	</section>

	<section class="card">

		<div class="card-header">
			<div class="card-title">
				Thumbnails
			</div>
		</div>

		<div class="card-body">

			<div class="row">
				<div class="col-md-6 source">
<a href="#" class="thumbnail">
	<img src="https://via.placeholder.com/300x200.png" loading="lazy">
</a>
				</div>

				<div class="col-md-6 code">
				</div>
			</div>
		</div>
	</section>

	<section class="card">

		<div class="card-header">
			<div class="card-title">
				Chat
			</div>
		</div>

		<div class="card-body">

			<div class="row">
				<div class="col-md-6 source">
<div class="bubbles">
	<div class="bubble remote">
		Knock knock!
	</div>

	<div class="bubble local">
		Who's there?
	</div>

	<div class="bubble event">
		User signed off
	</div>
</div>
				</div>

				<div class="col-md-6 code">
				</div>
			</div>
		</div>
	</section>

</main>

<script>
	$('a[href="#"]').click(function(e){ e.preventDefault(); });
	$('.code').each(function(){
		$(this).text( $(this).closest('.row').find('.source').html().trim() );
	});
</script>
