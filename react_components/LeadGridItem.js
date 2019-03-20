import React, {Component} from 'react';
import API from './Api.js';
import format from 'string-format';
import {Link} from "react-router-dom";
import StatusLabel from "./StatusLabel";

export default class LeadGridItem extends Component {

    constructor(props) {
        super(props);

        this.id = "lead-box-" + this.props.id;
        this.state = {
            loading: false,
            isDeleted: false
        };
    }

    onDelete(id, e) {
        e.preventDefault();
        this.setState({loading: true});

        API.get(format('leads/delete/{0}', id))
            .then(res => res.data)
            .then(data => this.onDeleteFinish(data));
    }

    onDeleteFinish(response) {
        if (response.status)
            $("#" + this.id).fadeOut();

        this.setState({loading: false});
    }

    onClick() {

    }

    render() {
        const {id, name, leads} = this.props;
        const {loading} = this.state;

        return (
            <div
                id={this.id}
                className={"grid-box-container grid__col-xs-6 grid__col-sm-6 grid__col-md-6 grid__col-lg-3" + (loading ? " loading" : "") }>
                <div className="grid-box invoice-box lead-box">
                    <div className="col-md-9 status">
                        <span className="label" title={leads}>{`${leads} Leads`}</span>
                    </div>
                    <div className="col-md-3 text-right ellipsis">
                        <i className="far fa-ellipsis-v fc-dropdown--trigger1"/>
                        <div className="fc-dropdown profile-dropdown">
                            abc
                        </div>
                    </div>
                    <div className="clearfix"/>
                    <div className="col-md-12 amount title">
                        <Link to={`/leads/edit/${id}`}><h4>{name}</h4></Link>
                    </div>
                    <div className="clearfix"/>
                    <div className="grid-footer">
                        <div className="row">
                            <div className="col-md-3">
                                <a href="#" onClick={(e) => this.onDelete(id, e)}><i className="far fa-trash-alt"/></a>
                            </div>
                            <div className="col-md-9 text-right">
                                <a target="_blank" href={`quotation/qid/${id}`}>Preview <i className="fa fa-eye"/></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}