define('crm:handlers/lead/detail-actions', [], function () {
    function LeadDetailActions(view) {
        this.view = view;
    }

    LeadDetailActions.prototype.findContacts = function () {
        var view = this.view;
        var model = view.model;
        var id = model && model.id;
        var email = model && model.get ? model.get('emailAddress') : null;

        if (!email) {
            view.notify('Lead has no email address', 'warning');
            return;
        }

        view.notify('Searching contacts…', 'info');

        Espo.Ajax.postRequest('Lead/action/findContacts', { id: id })
            .then(function (response) {
                var names = response.names || [];
                var message;

                if (names.length) {
                    message = 'Found ' + names.length + ' contacts: ' + names.join(', ');
                } else {
                    message = 'No contacts found with the same email.';
                }

                view.notify(message, 'success');
            })
            .catch(function () {
                view.notify('Failed to search contacts', 'error');
            });
    };

    return LeadDetailActions;
});