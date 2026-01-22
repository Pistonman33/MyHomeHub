<section>
    <div class="container-fluid">
        <div class="filter-block">
            <div class="row">
                <div class="col-12">
                    <form class="form-inline">
                        @if (isset($all_genre) && sizeof($all_genre)>0)
                        <div class="form-group m-2">
                            <div class="dropdown">
                                <input type="hidden" name="genre_select" id="genre_select" value="-1" />        
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLinkGenre" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ $filter_genre }}
                                </a>
                            
                                <div class="dropdown-menu dropdown-multicol" aria-labelledby="dropdownMenuLink">
                                    <a class="dropdown-item dropdown_genre" choice="-1">All genre</a>
                                    <?php $cpt = 0; ?>
                                    @foreach ($all_genre as $genre) 
                                        @if ($cpt == 0)
                                            <div class="dropdown-row">
                                        @endif
                                        <a class="dropdown-item dropdown_genre" choice="{{ $genre['genre'] }}">{{ $genre['genre'] }}</a>
                                        <?php $cpt++; ?>
                                        @if ($cpt % 3 == 0)
                                            </div>
                                            <div class="dropdown-row">
                                        @endif
                                    @endforeach
                                            </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if (isset($all_support) && sizeof($all_support)>0)
                        <div class="form-group m-2">
                            <div class="dropdown">
                                <input type="hidden" name="support_select" id="support_select" value="-1" />
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLinkSupport" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ $filter_support }}
                                </a>
                            
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <a class="dropdown-item dropdown_support" choice="-1">All support</a>
                                    @foreach ($all_support as $support) 
                                        <a class="dropdown-item dropdown_support" choice="{{ $support->id }}">{{ $support->type }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                        @if (isset($all_year) && sizeof($all_year)>0)
                        <div class="form-group m-2">
                            <div class="dropdown">
                                <input type="hidden" name="year_select" id="year_select" value="-1" />
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLinkYear" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ $filter_year }}
                                </a>
                            
                                <div class="dropdown-menu dropdown-multicol5" aria-labelledby="dropdownMenuLink">
                                    <a class="dropdown-item dropdown_year" choice="-1">All year</a>
                                    <?php $cpt = 0; ?>
                                    @foreach ($all_year as $year) 
                                        @if ($cpt == 0)
                                            <div class="dropdown-row5">
                                        @endif
                                        <a class="dropdown-item dropdown_year" choice="{{ $year['year'] }}">{{ $year['year']}}</a>
                                        <?php $cpt++; ?>
                                        @if ($cpt % 5 == 0)
                                            </div>
                                            <div class="dropdown-row5">
                                        @endif
                                    @endforeach
                                            </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-filter mb-1">
                    <div class="filter-selection">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-filter-selection active">
                            <input type="radio" name="options" id="option1" autocomplete="off" checked> New
                            </label>
                            <label class="btn btn-filter-selection">
                            <input type="radio" name="options" id="option2" autocomplete="off"> Recent
                            </label>
                            <label class="btn btn-filter-selection">
                            <input type="radio" name="options" id="option3" autocomplete="off"> Popular
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 mb-1 ml-2">
                    Nb {{ $type }}:<span id="nb_movies">-</span>
                </div>
            </div>
        </div>
    </div>
  </div>
</section>
    