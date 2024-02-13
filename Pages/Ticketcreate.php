<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-body">
                    <p class="card-text">
                        Przez utworzeniem nowego zgłoszenia, upewnij się że <b>Twoje pytanie nie znajduje się na liście często zadawanych pytań</b> w przeciwnym wypadku, nie otrzymasz odpowiedzi.
                        Informujemy, że zgodnie z naszym regulaminem pomoc z problemami niezwiązanymi z naszą winą jest dodatkowo płatna, szczegółów udzieli osoba, która zajmie się zgłoszeniem.
                        Czas oczekiwania na odpowiedź wynosi do 72 godzin, jednak najczęściej trwa to kilka godzin.
                    </p>
                </div>
            </div>
            </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form id="ticket">
                        <!--<div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Problem</label>
                            <select class="form-select" name="problem" id="exampleFormControlSelect1">
                                <option selected value="0" disabled>Wybierz problem z listy</option>
                                <option value="1">Bot nie łączy się z serwerem TS3</option>
                                <option value="2">Bot nie łączy się z MySQL</option>
                                <option value="3">Bot łączy się, ale zostaje wyrzucony za flood</option>
                                <option value="4">Bot wychodzi z serwera TS3 po jakimś czasie</option>
                                <option value="5">Funkcja x nie działa</option>
                                <option value="6">Mam propozycję w sprawie aplikacji</option>
                                <option value="7">Mam problem z licencją / licencja jest odrzucana</option>
                                <option value="8">Kradzież aplikacji (zgłoszenie)</option>
                            </select>
                        </div>!-->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="true" name="agree" id="defaultCheck1">
                            <label class="form-check-label" for="defaultCheck1"> Potwierdzam zapoznanie się z często zadawanymi pytaniami </label>
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Priorytet</label>
                            <select class="form-select" name="priority" id="exampleFormControlSelect1">
                                <option selected value="2">Niski</option>
                                <option value="1">Normalny</option>
                                <option value="0">Ważny</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Temat</label>
                            <input class="form-control" type="text" name="topic">
                        </div>
                        <div class="mb-3">
                            <label>Opis problemu</label>
                            <textarea class="form-control" rows="10" name="message"></textarea>
                        </div>
                        <button type="submit" id="sub" class="btn btn-primary">Wyślij</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>


$("#ticket").submit(function(e) {

    $("#sub").prop("disabled", true);
    e.preventDefault(); // avoid to execute the actual submit of the form.
    var form = $(this);
    $.ajax({
        type: "POST",
        url: "<?=URL?>/Api/TicketCreate",
        data: form.serialize(), // serializes the form's elements.
        success: function(data)
        {
        data = JSON.parse(data);
        if(data.success) {
            toastr.success(data.message);
            setTimeout(function(){
            window.location.href = '<?=URL?>/Tickets';
            }, 1000);
        } else {
            $("#sub").prop("disabled", false);
            toastr.error(data.message);
        }
        console.log(data);
        }
    });
});


</script>