<section id="main">
    <h1 class="showcase-heading">Recently watched</h1>
    <ul id="autoWidth" class="cs-hidden">
        @foreach($showcases as $showcase)  
        <li>
            <div class="showcase-box">
                <img src="{{ url("storage/images/$type/$showcase->poster") }}">
            </div>        
        </li>
        @endforeach
    </ul>
</section>
