<!-- action buttons -->
@include('pages.notes.components.misc.list-page-actions')
<!-- action buttons -->

@if(request('noteresource_type') == 'client')
<div class="alert alert-warning m-b-15" role="alert">
	<strong>NO VISIBLE PARA EL CLIENTE.</strong> Este espacio es interno: acá podés cargar minutas de reunión,
	comentarios y cualquier nota útil. Todo se utiliza para el análisis de salud del cliente. Recomendación:
	incluir fecha en cada nota.
</div>
@endif

<!--notes table-->
<div class="card-embed-fix">
@include('pages.notes.components.table.wrapper')
</div>
<!--notes table-->