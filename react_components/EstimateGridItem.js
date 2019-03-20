import React, {Component} from 'react';
import API from './Api.js';
import format from 'string-format';
import {Link} from "react-router-dom";
import StatusLabel from "./StatusLabel";
import Util from "./Util";
import Avatar from "./Avatar";

export default class EstimateGridItem extends Component {

    constructor(props) {
        super(props);

        this.id = "estimate-box-" + this.props.id;
        this.state = {
            loading: false,
            isDeleted: false
        };
    }

    onDelete(id) {
        this.setState({loading: true});

        API.get(format('estimates/delete/{0}', id))
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

    render() {
        const {id, reference, company, issue_date, estimate_status, due_date, sum, currency, sent_date, client, paid_date} = this.props;
        const {loading} = this.state;

        return (
            <div
                id={this.id}
                className={"grid-box-container grid__col-xs-6 grid__col-sm-6 grid__col-md-6 grid__col-lg-3" + (loading ? " loading" : "") }>
                <div className="grid-box invoice-box" onClick={this.onClick.bind(this)}>
                    <div className="col-md-6 status">
                        <StatusLabel status={estimate_status}/>
                    </div>
                    <div className="col-md-6 text-right ellipsis clickable">
                        <button type="button" className="dropdown-toggle" data-toggle="dropdown"
                                aria-expanded="false">
                            <i className="far fa-ellipsis-v"/>
                        </button>
                        <ul className="dropdown-menu dropdown-menu--small" role="menu">
                            <li>
                                <Link to={`estimates/edit/${id}`}>Edit</Link>
                            </li>
                            <li>
                                <a href="#" className="estimate-delete-trigger"
                                   data-href={`invoices/delete/${id}`}>Delete</a>
                            </li>
                        </ul>
                    </div>
                    <div className="clearfix"/>
                    <div className="text-container">
                        <div className="col-md-7 amount">
                            <Link to={`estimates/edit/${id}`} className="clickable">
                                <h4>{currency}{sum.formatMoney()}</h4></Link>
                        </div>
                        <div className="col-md-5 text-right reference">
                            <span>{reference ? `#${reference}` : ''}</span>
                        </div>
                        <div className="clearfix"/>
                        <div className="col-md-12 company">
                            <h5>{company ? company.name : ' '}</h5>
                        </div>
                        <div className="clearfix"/>
                        <div className="col-md-12 client-info">
                            <div className="user-image">
                                {client &&
                                <Avatar {...client} />
                                }
                                {!client &&
                                <Avatar placeholder={true}/>
                                }
                            </div>
                            <div className="user-info">
                                <h5>{client ? client.firstname : ' '}</h5>
                                <span>{client ? client.email : ' '}</span>
                            </div>
                        </div>
                    </div>
                    <div className="grid-footer">
                        <div className="datetime">
                            {estimate_status === 'Paid' &&
                            <span><i className="far fa-calendar-alt"/>&nbsp;&nbsp;<span
                                style={{
                                    fontSize: 12,
                                    color: "#9A9A9A"
                                }}>Paid on {Util.getDateHuman(paid_date, true)}</span></span>
                            }
                            {estimate_status === 'Sent' &&
                            <span><i className="far fa-calendar-alt"/>&nbsp;&nbsp;<span
                                style={{
                                    fontSize: 12,
                                    color: "#9A9A9A"
                                }}>Sent on {Util.getDateHuman(sent_date, true)}</span></span>
                            }
                        </div>
                        <div className="trash">
                            <button className="estimate-delete-trigger clickable" data-href={`invoices/delete/${id}`}><i
                                className="far fa-trash-alt"/></button>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}