@extends('layouts.main')

@section('title', 'Track you expenses')
@section('content')

<body id="add" class="main-body">
    @extends('layouts.navbar')

    <main id="bank-section" class="main">
        <section id="bank-wrap">
            <form action="/import/choose-bank" method="post" id="citadele-form">
            @csrf
                <div class="bank-card" id="citadele">
                    <img src="https://cdn-logos.gocardless.com/ais/CITADELE_PARXLV22.png" alt="">
                    <h3 class="bank-title">Citadele</h3>
                    <input type="hidden" name="iban" value="CITADELE_PARXLV22">
                </div>
            </form>
            <form action="/import/choose-bank" method="post" id="luminor-form">
            @csrf
                <div class="bank-card" id="luminor">
                    <img src="https://cdn-logos.gocardless.com/ais/LUMINOR_NDEAEE2X.png" alt="">
                    <h3 class="bank-title">Luminor</h3>
                    <input type="hidden" name="iban" value="LUMINOR_RIKOLV2X">
                </div>
            </form>
            <form action="/import/choose-bank" method="post" id="seb-form">
            @csrf
                <div class="bank-card" id="seb">
                    <img src="https://cdn-logos.gocardless.com/ais/SEB_SE_CORP_ESSESESS.png" alt="">
                    <h3 class="bank-title">SEB</h3>
                    <input type="hidden" name="iban" value="SEB_UNLALV2X">
                </div>
            </form>
            <form action="/import/choose-bank" method="post" id="swedbank-form">
            @csrf
                <div class="bank-card" id="swedbank">
                    <img src="https://cdn-logos.gocardless.com/ais/SWEDBANK_LONG_SWEDSESS.png" alt="">
                    <h3 class="bank-title">Swedbank</h3>
                    <input type="hidden" name="iban" value="SWEDBANK_HABALV22">
                </div>
            </form>
        </section>
    </main>
</body>

<script>
    document.getElementById('citadele').addEventListener('click', function() {
        document.getElementById('citadele-form').submit();
    });
    document.getElementById('luminor').addEventListener('click', function() {
        document.getElementById('luminor-form').submit();
    });
    document.getElementById('seb').addEventListener('click', function() {
        document.getElementById('seb-form').submit();
    });
    document.getElementById('swedbank').addEventListener('click', function() {
        document.getElementById('swedbank-form').submit();
    });
</script>
@endsection