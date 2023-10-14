function submitForm() {
    var userCode = $('input[name="code_lottery"]').val();
    console.log("Code saisi : " + userCode);

    $.post('?page=loterie', { code_lottery: userCode }, function(response) {
        // Masquer le formulaire de participation
        $('#lottery-form').hide();

        // Afficher le résultat de la loterie
        var lotteryResultElement = $('#lottery-result');
        lotteryResultElement.empty();

        if (response === 'CODE_INVALIDE') {
            lotteryResultElement.text("Le code n'est pas valide, ou déjà utilisé.");
        } else {
            lotteryResultElement.html(response);
        }

        lotteryResultElement.show();
    }).fail(function() {
        console.error('Erreur lors de la requête AJAX.');
    });
}
