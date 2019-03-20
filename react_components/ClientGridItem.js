import React, {Component} from 'react';
import API from './Api.js';
import format from 'string-format';
import {Link} from "react-router-dom";
import StatusLabel from "./StatusLabel";
import WorkerAvatars from "./WorkerAvatars";
import ProgressBar from "./ProgressBar";
import Avatar from "./Avatar";


export default class ClientGridItem extends Component {

    constructor(props) {
        super(props);

        this.id = "people-box-" + this.props.id;
        this.state = {
            loading: false,
            isDeleted: false
        };
    }

    onDelete(e, id) {
        e.preventDefault();
        this.setState({loading: true});

        API.get(`clients/delete_json/${id}`)
            .then(res => res.data)
            .then(data => this.onDeleteFinish(data));
    }

    onDeleteFinish(response) {
        if (response.status)
            $("#" + this.id).fadeOut();

        this.setState({loading: false});
    }

    onInvite(){
        // if(this.props.id){
        //     API.get(`clients/credentials/${this.props.id}/TRUE/FALSE`);
        // }
        return `clients/credentials/${this.props.id}/TRUE/FALSE`;
    }

    onClick() {

    }

    render() {
        const {id, username, firstname, lastname, userpic, title, status, last_login} = this.props;
        const {loading} = this.state;

        return (
            <div
                id={this.id}
                className={"grid-box-container grid__col-xs-6 grid__col-sm-6 grid__col-md-6 grid__col-lg-3" + (loading ? " loading" : "") }>
                <div className="grid-box invoice-box people-box">
                    <div className="col-md-12 grid-header">
                        <div><i className="fa fa-star-o"/></div>
                        <div><span>{username}</span></div>
                        <div><a href={`/clients/update/${id}/FALSE`} data-toggle="mainmodal"><i
                            className="far fa-edit"/></a></div>
                    </div>
                    <div className="text-center">
                        <Avatar firstname={firstname} lastname={lastname} userpic={userpic}/>
                    </div>
                    <div className="clearfix"/>
                    <div className="col-md-12 amount">
                        <a href={`/projects/edit/${id}/tasks`}><h4>{`${firstname} ${lastname}`}</h4></a>
                    </div>
                    <div className="clearfix"/>
                    <div className="col-md-12 company">
                        <h5>{title}</h5>
                    </div>
                    <div className="clearfix"/>
                    <div className="col-md-12 text-center buttons hide">
                        <StatusLabel status="sent" text={status}/>&nbsp;
                        <StatusLabel status="sent" text="Admin"/>
                    </div>
                    <div className="clearfix"/>
                    <div className="grid-footer">
                        <div>
                            <a href="#" className="transparent" onClick={(e) => this.onDelete(e, id)}><i
                                className="far fa-trash-alt"/></a>
                        </div>

                        <div>
                            {/* {last_login && (<span>Logged in {moment(last_login).format('lll')}</span>)} */}
                        </div>
                        <div>
                            {/*<a href="#" className="transparent" onClick={this.onInvite.bind(this)}><i
                                className="fa fa-envelope"/></a>*/}
                            <a href="#" className="transparent client-invite-trigger"
                               data-href={this.onInvite()}><i
                                className="fa fa-envelope"/></a>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}