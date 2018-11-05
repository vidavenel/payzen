<form method="POST" action="https://secure.payzen.eu/vads-payment/" id="{{ $id_form }}">
    @foreach($vads as $k => $value)
        <input type="hidden" name="{{ $k }}" value="{{ $value }}"/>
    @endforeach
        <input type="hidden" name="signature" value="{{ $signature }}">
</form>