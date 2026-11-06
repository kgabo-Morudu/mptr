<?php
$role = Auth::role();
$route = $_GET['route'] ?? 'dashboard';
$items = [
    ['dashboard',      'Dashboard',           'speedometer2', ['admin','manager','supervisor','technician','requester']],
    ['facilities',     'Facilities',          'building',      ['admin','manager']],
    ['buildings',      'Buildings',           'building-add',  ['admin','manager']],
    ['assets',         'Assets',              'tools',         ['admin','manager','supervisor','technician']],
    ['requests',       'Requests',            'envelope-paper',['admin','manager','supervisor','technician','requester']],
    ['workorders',     'Work Orders',         'clipboard-check',['admin','manager','supervisor','technician']],
    ['technicians',    'Technicians',         'people',        ['admin','manager','supervisor']],
    ['costs',          'Costs',               'cash-stack',    ['admin','manager','supervisor']],
    ['reports',        'Reports',             'bar-chart',     ['admin','manager','supervisor']],
    ['users',          'Users',               'person-gear',   ['admin']],
];
?>
<aside class="sidebar bg-dark text-white">
    <ul class="nav flex-column p-2">
    <?php foreach ($items as [$r, $label, $icon, $allowed]): 
        if (!in_array($role, $allowed, true)) continue; ?>
        <li class="nav-item">
            <a href="<?= BASE_URL ?>/index.php?route=<?= $r ?>" 
               class="nav-link text-white <?= $route === $r ? 'active bg-primary' : '' ?>">
                <i class="bi bi-<?= $icon ?> me-2"></i> <?= $label ?>
            </a>
        </li>
    <?php endforeach; ?>
    </ul>
</aside>