import React, {Component} from 'react';
import API from './Api.js';
import format from 'string-format';
import {Link, withRouter } from "react-router-dom";
import StatusLabel from "./StatusLabel";
import WorkerAvatars from "./WorkerAvatars";
import ProgressBar from "./ProgressBar";
import Avatar from "./Avatar";
import moment from 'moment';
import classnames from 'classnames';

class PeopleGridItem extends Component {

    constructor(props) {
        super(props);

        this.id = "people-box-" + this.props.id;
        this.state = {
            loading: false,
            isDeleted: false,
            status: props.status,
            admin: props.admin,
            favor: props.favor
        };
    }

    componentWillReceiveProps(props) {
        if (this.props !== props) {
            this.setState({
                status: props.status,
                admin: props.admin,
                favor: props.favor
            })
        }
    }
    getDeleteUrl(id, project_id) {
        if (project_id) {
            return `projects/edit/${project_id}/team/delete?user_id=${id}`;
        }
        return `settings/user_delete_json/${id}`;
    }

    onDelete(e, id, project_id) {
        e.preventDefault();
        this.setState({loading: true});

        if (project_id) {
            API.get(`projects/edit/${project_id}/team/delete?user_id=${id}`)
                .then(res => res.data)
                .then(data => this.onDeleteFinish(data));
        } else {
            API.get(`settings/user_delete_json/${id}`)
                .then(res => res.data)
                .then(data => this.onDeleteFinish(data));
        }
    }

    onDeleteFinish(response) {
        if (response.status)
            $("#" + this.id).fadeOut();

        this.setState({loading: false});
    }

    onToggleActive() {
        if(this.props.id){
            API.get(`team/edit/${this.props.id}/status/reverseactive`)
            .then(res => res.data)
            .then(data => this.onStatusUpdated(data));
                
        }
    }

    onToggleAdmin(){
        if(this.props.id){
            API.get(`team/edit/${this.props.id}/admin/reverseactive`)
            .then(res => res.data)
            .then(data => this.onStatusUpdated(data));
                
        }
    }
    onStatusUpdated(response){
        if (response.status) {
            this.setState({
                status: response.data.status,
                admin: response.data.admin
            });
        }
    }

    onInvite(){
        if(this.props.id){
            API.get(`team/inviteAgain/${this.props.id}`);
        }
    }

    onFavor(){
        if(this.props.id){
            API.get(`team/edit/${this.props.id}/favor/reverseactive`)
            .then(this.props.reload());
        }
    }

    onClick() {

    }

    render() {
        const {id, username, firstname, lastname, userpic, title, status, project_id, last_login,favor} = this.props;
        const {loading} = this.state;

        return (
            <div
                id={this.id}
                className={"grid-box-container grid__col-xs-6 grid__col-sm-6 grid__col-md-6 grid__col-lg-3" + (loading ? " loading" : "") }>
                <div className="grid-box invoice-box people-box">
                    <div className="col-md-12 grid-header">
                        <div>
                            <i onClick={this.onFavor.bind(this)} style={{color: "#EAAB10"}} className={classnames({'fa': this.state.favor == 1, 'far':this.state.favor == 0, 'fa-star': true})} />
                        </div>
                        <div><span>@{username}</span></div>
                        <div><a href={`/settings/user_update/${id}`} data-toggle="mainmodal"><i
                            className="far fa-edit"/></a></div>
                    </div>
                    <div className="text-center">
                        <Avatar firstname={firstname} lastname={lastname} userpic={userpic}/>
                    </div>
                    <div className="clearfix"/>
                    <div className="col-md-12 amount">
                        <h4>{`${firstname} ${lastname}`}</h4>
                    </div>
                    <div className="clearfix"/>
                    <div className="col-md-12 company">
                        <h5>{title}</h5>
                    </div>
                    <div className="clearfix"/>
                    <div className="col-md-12 text-center buttons">
                        <StatusLabel status={this.state.status} text={this.state.status} onClick={this.onToggleActive.bind(this)} />&nbsp;&nbsp;
                        <StatusLabel status={this.state.admin} text="Admin" onClick={this.onToggleAdmin.bind(this)} />
                    </div>
                    <div className="clearfix"/>
                    <div className="grid-footer">
                        <div>
                            <a href="#" className="transparent people-delete-trigger"
                               data-href={this.getDeleteUrl(id, project_id)}><i
                                className="far fa-trash-alt"/></a>
                        </div>
                        <div>
                            {last_login && (<span>Logged in {moment(last_login).format('lll')}</span>)}
                        </div>
                        <div>
                            <a  className="transparent" onClick={this.onInvite.bind(this)}><i
                                className="far fa-envelope"/></a>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default withRouter(PeopleGridItem);
