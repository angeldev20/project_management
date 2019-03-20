import React, {Component} from 'react';
import API from './Api.js';
import format from 'string-format';
import {Link} from "react-router-dom";
import StatusLabel from "./StatusLabel";
import WorkerAvatars from "./WorkerAvatars";
import ProgressBar from "./ProgressBar";

const Avatar = ({firstname, lastname}) => {
    let initials = firstname.substring(0, 1) + lastname.substring(0, 1);
    return <a className="initials-avatar-circle tt" title="" data-original-title={name}>{initials}</a>
};

export default class ProjectGridItem extends Component {

    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.id = "project-box-" + this.props.id;
        this.state = {
            loading: false,
            isDeleted: false,
            sticky: props.sticky
        };
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    onDelete(id) {
        this.setState({loading: true});

        API.get(format('invoices/delete/{0}', id))
            .then(res => res.data)
            .then(data => this.onDeleteFinish(data));
    }

    onDeleteFinish(response) {
        if (response.status)
            $("#" + this.id).fadeOut();

        this.setState({loading: false});
    }

    onClick(e) {
        var $hasClass = $(e.target).hasClass("clickable");
        var $parentHasClass = $(e.target).parents(".clickable").length > 0;

        if (!$hasClass && !$parentHasClass) {
            const {onClick} = this.props;

            onClick(this.props);
        }
    }

    makeFavourite(fav = "0") {
        const {id} = this.props;

        API.get(`/projects/sticky_json/${id}`);

        this.updateState({
            sticky: fav
        });
    }

    render() {
        const {id, name, company, progress, done_tasks, total_tasks, start_human, workers, tracking} = this.props;
        const {loading, sticky} = this.state;

        let {due_in} = this.props;
        let status = due_in.indexOf('Overdue') >= 0 ? 'Overdue' : 'Draft';

        if (progress >= 100) {
            status = 'Paid';
            due_in = 'Completed';
        }

        return (
            <div
                id={this.id}
                className={"grid-box-container grid__col-xs-6 grid__col-sm-6 grid__col-md-6 grid__col-lg-3" + (loading ? " loading" : "") }>
                <div className="grid-box invoice-box project-box" onClick={this.onClick.bind(this)}>
                    <div className="col-md-7 status">
                        <StatusLabel status={status} text={due_in} filled={status === 'Overdue'}/>
                    </div>
                    <div className="col-md-5 text-right ellipsis clickable">
                        <div>
                            {sticky === "1" &&
                            <i onClick={() => this.makeFavourite("0")} className="fa fa-star"
                               style={{color: "#EAAB10"}}/>
                            }
                            {sticky !== "1" &&
                            <i onClick={() => this.makeFavourite("1")} className="far fa-star"/>
                            }
                        </div>
                        <div>
                            <button type="button" className="dropdown-toggle" data-toggle="dropdown"
                                    aria-expanded="false">
                                <i className="far fa-ellipsis-v"/>
                            </button>
                            <ul className="dropdown-menu dropdown-menu--small" role="menu">
                                <li>
                                    <a href={`/projects/update/${id}`} data-toggle="mainmodal">Edit Project</a>
                                </li>
                                <li>
                                    <a href={`/projects/tracking/${id}`}>{tracking ? 'Stop' : 'Start'} Timer</a>
                                </li>
                                <li>
                                    <a href="#" className="project-delete-trigger" data-href={`projects/delete/${id}`}>Delete</a>
                                </li>
                                <li>
                                    <a href={`/projects/copy/${id}`} data-toggle="mainmodal">Duplicate</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div className="clearfix"/>
                    <div className="col-md-12 progress-container">
                        <ProgressBar color={status} progress={progress}/>
                    </div>
                    <div className="clearfix"/>
                    <div className="text-container">
                        <div className="col-md-12 amount">
                            <a href={`/projects/view/${id}/tasks`} className="clickable"><h4>{name}</h4></a>
                        </div>
                        <div className="clearfix"/>
                        <div className="col-md-12 company">
                            <h5>{company ? company.name : ' '}</h5>
                        </div>
                        <div className="clearfix"/>
                        <div className="col-md-12 workers">
                            <WorkerAvatars workers={workers.map(w => w.worker)} limit={5} allowAdd={false}/>
                        </div>
                        <div className="clearfix"/>
                    </div>
                    <div className="grid-footer">
                        <div>
                            <i className="far fa-calendar-alt"/>&nbsp;{start_human}
                        </div>
                        <div>
                            <i className="far fa-check-circle"/>&nbsp;{done_tasks}/{total_tasks}
                        </div>
                        <div>
                            <a href="#" className="transparent clickable"><i
                                className="far fa-copy"/></a>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}