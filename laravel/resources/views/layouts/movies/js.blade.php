<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
<script src="{{ asset('js/movies/lightslider.js') }}"></script>
<script>
  // global javascript variables
  var urlsearch = window.location.href.includes('tvshows') ? "{{ URL::to('tvshows/filter') }}" : "{{ URL::to('movies/filter') }}";
  var loadingImg = '{{ URL::asset('img/loading.gif') }}';
  var csrfToken = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/movies/filter.js')}}"></script>
