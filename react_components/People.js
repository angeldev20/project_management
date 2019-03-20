import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import {BrowserRouter as Router, Route, Switch} from 'react-router-dom';
import Projects from "./Projects";
import Tickets from "./Tickets";
import EditTicket from "./EditTicket";
import PeopleTeam from "./PeopleTeam";
import PeopleClients from "./PeopleClients";

class People extends Component {
    render() {
        return (
            <Router>
                <Switch>
                    <Route exact path="/team" component={PeopleTeam}/>
                    <Route exact path="/clients" component={PeopleClients}/>
                </Switch>
            </Router>
        );
    }
}

var elem = document.getElementById('people-section');

if (elem) {
    ReactDOM.render(<People />, document.getElementById('people-section'));
}