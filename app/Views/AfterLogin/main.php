<!DOCTYPE html>
<html lang="en">
  <head>
    <?= $header; ?>
  </head>
  <body>
    <div class="container-scroller">
		<!-- partial:partials/_horizontal-navbar.html -->
    <div class="horizontal-menu">
        <?= $topbar; ?>
    </div>
    <!-- partial -->
		<div class="container-fluid page-body-wrapper">
			<div class="main-panel">
				<div class="content-wrapper">
            <?= $content; ?>
				</div>
				<!-- content-wrapper ends -->
      <?= $footer; ?>
  </body>
</html>