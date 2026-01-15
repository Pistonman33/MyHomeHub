<?php
use Diglactic\Breadcrumbs\Breadcrumbs;

use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('home'));
});

// Register
Breadcrumbs::for('register', function (BreadcrumbTrail $trail) {
    $trail->push('Register', route('register'));
});


// Home > Finance
Breadcrumbs::for('finance', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Finance', route('finance'));
});

// Home > Finance > Add transactions
Breadcrumbs::for('finance.import', function (BreadcrumbTrail $trail) {
    $trail->parent('finance');
    $trail->push("Add transactions", route('finance.import'));
});

// Home > Finance > Transactions uploaded
Breadcrumbs::for('finance.import_post', function (BreadcrumbTrail $trail) {
    $trail->parent('finance');
    $trail->push("Transactions uploaded", route('finance.import_post'));
});

// Home > Finance > Update transactions
Breadcrumbs::for('finance.show', function (BreadcrumbTrail $trail) {
    $trail->parent('finance');
    $trail->push("Update transactions", route('finance.show'));
});

// Home > Finance > Update transactions
Breadcrumbs::for('finance.show_post', function (BreadcrumbTrail $trail) {
    $trail->parent('finance');
    $trail->push("Update transactions", route('finance.show'));
});

// Home > Finance > Update transactions
Breadcrumbs::for('finance.show_with_offset', function (BreadcrumbTrail $trail) {
    $trail->parent('finance');
    $trail->push("Update transactions", route('finance.show'));
});





// Home > Finance > All transactions
Breadcrumbs::for('finance.all', function (BreadcrumbTrail $trail) {
    $trail->parent('finance');
    $trail->push("All transactions", route('finance.all'));
});

// Home > Charge
Breadcrumbs::for('charge', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Charge', route('charge'));
});
// Home > Charge > Save Charge
Breadcrumbs::for('charge.save', function (BreadcrumbTrail $trail) {
    $trail->parent('charge');
    $trail->push('Save charge', route('charge.save'));
});
// Home > Charge > Save Charge
Breadcrumbs::for('charge.save_post', function (BreadcrumbTrail $trail) {
    $trail->parent('charge');
    $trail->push('Save charge', route('charge.save'));
});

// Home > Stats
Breadcrumbs::for('stats', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Stats', route('stats'));
});

// Home > Stats > Show
Breadcrumbs::for('stats.show', function (BreadcrumbTrail $trail) {
    $trail->parent('stats');
    $trail->push('Show Stats', route('stats'));
});

// Home > Documents
Breadcrumbs::for('documents', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Documents', route('documents'));
});

// Home > Movies
Breadcrumbs::for('movies', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Movies', route('movies'));
});

// Home > Movies > All movies
Breadcrumbs::for('movies.all', function (BreadcrumbTrail $trail) {
    $trail->parent('movies');
    $trail->push("All movies", route('movies.all'));
});

// Home > Movies > Pending Movie(s)
Breadcrumbs::for('movies.pending', function (BreadcrumbTrail $trail) {
    $trail->parent('movies');
    $trail->push("Pending Movie(s)", route('movies.pending'));
});

// Home > Movies > Pending Movie(s)
Breadcrumbs::for('movies.pending_with_offset', function (BreadcrumbTrail $trail) {
    $trail->parent('movies');
    $trail->push("Pending Movie(s)", route('movies.pending'));
});


// Home > Movies > Pending Movie(s)
Breadcrumbs::for('movies.pending_post', function (BreadcrumbTrail $trail) {
    $trail->parent('movies');
    $trail->push("Pending Movie(s)", route('movies.pending'));
});

// Home > Movies > Pending Movie(s)
Breadcrumbs::for('movies.checkPicture', function (BreadcrumbTrail $trail) {
    $trail->parent('movies');
    $trail->push("Check Picture(s)", route('movies.checkPicture'));
});

// Home > TV Shows
Breadcrumbs::for('tvshows', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('TV Shows', route('tvshows'));
});

// Home > TV Shows > Pending Movie(s)
Breadcrumbs::for('tvshows.pending', function (BreadcrumbTrail $trail) {
    $trail->parent('tvshows');
    $trail->push("Pending Serie(s)", route('tvshows.pending'));
});

// Home > TV Shows > Pending Movie(s)
Breadcrumbs::for('tvshows.pending_with_offset', function (BreadcrumbTrail $trail) {
    $trail->parent('tvshows');
    $trail->push("Pending Serie(s)", route('tvshows.pending'));
});


// Home > TV Shows > Pending Movie(s)
Breadcrumbs::for('tvshows.pending_post', function (BreadcrumbTrail $trail) {
    $trail->parent('tvshows');
    $trail->push("Pending Serie(s)", route('tvshows.pending'));
});

// Home > TV Shows > Pending Movie(s)
Breadcrumbs::for('tvshows.checkPicture', function (BreadcrumbTrail $trail) {
    $trail->parent('tvshows');
    $trail->push("Check Picture(s)", route('tvshows.checkPicture'));
});

// Home > Backup
Breadcrumbs::for('backup', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Backup', route('backup'));
});
