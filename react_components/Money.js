import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import {BrowserRouter as Router, Route, Switch} from 'react-router-dom';
import Invoices from "./Invoices";
import TeamInvoices from "./TeamInvoices";
import Estimates from "./Estimates";
import Expenses from "./Expenses";
import Leads from "./Leads";
import EditInvoice from "./EditInvoice";
import EditTeamInvoice from "./EditTeamInvoice";
import EditEstimate from "./EditEstimate";
import Items from "./Items";
import EditLead from "./EditLead";
import CreateLead from "./CreateLead";

class Money extends Component {
    render() {
        return (
            <Router>
                <Switch>
                    <Route exact path="/invoices" component={Invoices}/>
                    <Route exact path="/invoices/edit/:id" component={EditInvoice}/>
                    <Route exact path="/tinvoices" component={TeamInvoices}/>
                    <Route exact path="/tinvoices/edit/:id" component={EditTeamInvoice}/>
                    <Route exact path="/estimates" component={Estimates}/>
                    <Route exact path="/estimates/edit/:id" component={EditEstimate}/>
                    <Route exact path="/expenses" component={Expenses}/>
                    <Route exact path="/items" component={Items}/>
                    <Route exact path="/leads" component={Leads}/>
                    <Route exact path="/leads/create" component={CreateLead}/>
                    <Route exact path="/leads/edit/:id" component={EditLead}/>
                </Switch>
            </Router>
        );
    }
}

var elem = document.getElementById('money-section');

if (elem) {
    ReactDOM.render(<Money />, document.getElementById('money-section'));
}