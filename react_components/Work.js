import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import {BrowserRouter as Router, Route, Switch} from 'react-router-dom';
import Projects from "./Projects";
import Tickets from "./Tickets";
import EditTicket from "./EditTicket";

class Work extends Component {
    render() {
        return (
            <Router>
                <Switch>
                    <Route exact path="/projects" component={Projects}/>
                    <Route exact path="/tickets" component={Tickets}/>
                    <Route exact path="/tickets/edit/:id" component={EditTicket}/>
                </Switch>
            </Router>
        );
    }
}

var elem = document.getElementById('work-section');

if (elem) {
    ReactDOM.render(<Work />, document.getElementById('work-section'));
}