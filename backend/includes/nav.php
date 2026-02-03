<?php

// var_dump('nav.php');
// get the file name of the current script
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// var_dump($currentPage);

$pages = [
	'dashboard'   => 'dashboard',
	'users'       => 'users',
	'groups'      => 'groups',
	'docs'        => 'docs',
	'permissions' => 'permissions',
	'profile'     => 'profile'
];

if ( $currentPage == 'profile' ) {
	$pages = ['dashboard' => 'dashboard', 'profile' => 'profile'];
}

/* $currentPage must be set before include: e.g. $currentPage = 'dashboard'; */
$menu = [
	'dashboard'   => ['label'=>'Dashboard',   'icon'=>'fa-gauge',        'perm'=>null],
	'users'       => ['label'=>'Users',       'icon'=>'fa-users',        'perm'=>'users.view'],
	'groups'      => ['label'=>'Groups',      'icon'=>'fa-layer-group',  'perm'=>'groups.view'],
	'docs'        => ['label'=>'Documents',   'icon'=>'fa-file-lines',   'perm'=>'docs.view'],
	'permissions' => ['label'=>'Permissions', 'icon'=>'fa-shield-halved','perm'=>'users.manage'],
	'profile'     => ['label'=>'Profile',     'icon'=>'fa-user',         'perm'=>null],
];
?>
<nav class="topNav">
	<div class="navBrand">LHSA </div>

	<!-- hamburger -->
	<input type="checkbox" id="navToggle" class="navToggle">
	<label for="navToggle" class="navHamb"><i class="fa-solid fa-bars"></i></label>

	<ul class="navLinks">
		<?php foreach ($menu as $key=>$item):

			if ($item['perm'] && !can($item['perm'])) continue; ?>

			<?php if ( !isset($pages[$key]) ) continue; ?>

			<?php if ( $key == $pages[$key] ) { ?>
				<li>
					<a class="navLink <?= ($currentPage === $key) ? 'active' : '' ?>"

						<?php echo $key; ?>
						<?php $filepath = $key == 'docs' ? '../docs/index.php' : '../admin/' . $key . '.' . 'php'; ?>

						href="<?php echo $filepath ?>"> <i class="fa-fw fa-solid <?= $item['icon'] ?>"></i><span><?= $item['label'] ?></span>
					</a>
				</li>
			<?php } ?>

		<?php endforeach; ?>

		<!-- logout always visible -->
		<li><a class="navLink" href="./../auth/logout.php"><i class="fa-fw fa-solid fa-right-from-bracket"></i> </a></li>
	</ul>
</nav>