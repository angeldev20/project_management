import React, {Component} from 'react';
import API from './Api.js';
import Avatar from "./Avatar";
import Modal from "./Modal";
import AssignWorkerForm from "./AssignWorkerForm";

export default class WorkerAvatars extends Component {

    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {
            data: [],
            loaded: false
        };
    }

    componentWillReceiveProps(props) {
        this.props = props;

        this.componentWillMount();
    }

    componentWillMount() {
        const {project_id, task_id, workers} = this.props;

        if (project_id) {
            API.get(`projects/get/${project_id}/workers`)
                .then(res => res.data)
                .then(data => this.initialize(data));
        } else if (task_id) {
            API.get(`projects/tasks/${task_id}/workers`)
                .then(res => res.data)
                .then(data => this.initialize(data));
        } else if (workers) {
            this.updateState({
                data: workers,
                loaded: true
            });
        }
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    initialize(data) {
        if (data.status) {
            this.updateState({
                data: data.data,
                loaded: true
            });
        } else {
            this.updateState({
                loaded: true
            });
        }
    }

    render() {
        const {data, loaded} = this.state;
        const {allowAdd, limit, onAddNewClick} = this.props;

        return (
            <div className="avatars">
                {data.map(({firstname, lastname, userpic}, index) => {
                    if (!limit || (limit && index < limit))
                        return <Avatar key={index} firstname={firstname} lastname={lastname}
                                       userpic={userpic}/>;
                    return <span key={index}/>;
                })}
                {limit && data.length > limit &&
                <a href="#" className="btn-circle">+{data.length - limit}</a>
                }
                {allowAdd &&
                <a href="#" onClick={(e) => onAddNewClick(e)} className="btn-circle"><i className="far fa-plus"/></a>
                }
            </div>
        );
    }
}