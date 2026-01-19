<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Breadcrumbs::for('register', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Register', route('register'));
});

/*
|--------------------------------------------------------------------------
| =====================
| ADMIN
| =====================
|--------------------------------------------------------------------------
*/

Breadcrumbs::for('admin.dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Admin');
});

/*
|--------------------------------------------------------------------------
| Finance
|--------------------------------------------------------------------------
*/
Breadcrumbs::for('admin.finance.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Finance', route('admin.finance.index'));
});

Breadcrumbs::for('admin.finance.import', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.finance.index');
    $trail->push('Add transactions', route('admin.finance.import'));
});

Breadcrumbs::for('admin.finance.import.post', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.finance.index');
    $trail->push('Transactions uploaded');
});

Breadcrumbs::for('admin.finance.show', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.finance.index');
    $trail->push('Update transactions', route('admin.finance.show'));
});

Breadcrumbs::for('admin.finance.show.offset', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.finance.index');
    $trail->push('Update transactions', route('admin.finance.show'));
});

Breadcrumbs::for('admin.finance.show.post', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.finance.index');
    $trail->push('Update transactions');
});

Breadcrumbs::for('admin.finance.all', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.finance.index');
    $trail->push('All transactions', route('admin.finance.all'));
});

/*
|--------------------------------------------------------------------------
| Stats
|--------------------------------------------------------------------------
*/
Breadcrumbs::for('admin.stats.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Stats', route('admin.stats.index'));
});

Breadcrumbs::for('admin.stats.show', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.stats.index');
    $trail->push('Category stats');
});

/*
|--------------------------------------------------------------------------
| Library
|--------------------------------------------------------------------------
*/
Breadcrumbs::for('admin.library.scan', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Library scan', route('admin.library.scan'));
});

/*
|--------------------------------------------------------------------------
| Blog
|--------------------------------------------------------------------------
*/
Breadcrumbs::for('admin.blog.articles', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Blog articles', route('admin.blog.articles'));
});

/*
|--------------------------------------------------------------------------
| Movies
|--------------------------------------------------------------------------
*/
Breadcrumbs::for('movies.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Movies', route('movies.index'));
});

Breadcrumbs::for('admin.movies.all', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Movies', route('admin.movies.all'));
});

Breadcrumbs::for('admin.movies.pending', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.movies.all');
    $trail->push('Pending movies', route('admin.movies.pending'));
});

Breadcrumbs::for('admin.movies.pending.offset', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.movies.all');
    $trail->push('Pending movies', route('admin.movies.pending'));
});

Breadcrumbs::for('admin.movies.pending.post', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.movies.all');
    $trail->push('Pending movies');
});

/*
|--------------------------------------------------------------------------
| TV Shows
|--------------------------------------------------------------------------
*/
Breadcrumbs::for('admin.tvshows.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('TV Shows', route('admin.tvshows.index'));
});

Breadcrumbs::for('admin.tvshows.pending', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Pending series', route('admin.tvshows.pending'));
});

Breadcrumbs::for('admin.tvshows.pending.offset', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Pending series', route('admin.tvshows.pending'));
});

Breadcrumbs::for('admin.tvshows.pending.post', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Pending series');
});

/*
|--------------------------------------------------------------------------
| Backup
|--------------------------------------------------------------------------
*/
Breadcrumbs::for('admin.backup.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Backup', route('admin.backup.index'));
});
