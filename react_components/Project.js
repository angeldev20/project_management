import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import {BrowserRouter as Router, Route, Switch} from 'react-router-dom';
import ProjectTasks from "./ProjectTasks";
import ProjectTeam from "./ProjectTeam";
import API from './Api.js';

class Project extends Component {

    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {
        };
    }

    componentWillMount() {
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    render() {
        return (
            <Router>
                <Switch>
                    <Route exact path="/projects/view/:id/tasks" component={ProjectTasks}/>
                    <Route exact path="/projects/view/:id/team" component={ProjectTeam}/>
                </Switch>
            </Router>
        );
    }
}

var elem = document.getElementById('project-section');

if (elem) {
    var id = $(elem).data("project");
    ReactDOM.render(<Project id={id}/>, document.getElementById('project-section'));
}