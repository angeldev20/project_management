import React, {Component} from 'react';
import API from './Api.js';
import format from 'string-format';
import {Link} from "react-router-dom";
import StatusLabel from "./StatusLabel";
import Avatar from "./Avatar";
import Util from "./Util";

export default class InvoiceGridItem extends Component {

    constructor(props) {
        super(props);

        this.id = "invoice-box-" + this.props.id;
        this.state = {
            loading: false,
            isDeleted: false
        };
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

    render() {
        const {id, reference, company, issue_date, status, due_date, outstanding, currency, sent_date, client, paid_date} = this.props;
        const {loading} = this.state;

        return (
            <div
                id={this.id}
                className={"grid-box-container grid__col-xs-6 grid__col-sm-6 grid__col-md-6 grid__col-lg-3" + (loading ? " loading" : "") }>
                <div className="grid-box invoice-box" onClick={this.onClick.bind(this)}>
                    <div className="col-md-6 status">
                        <StatusLabel status={status}/>
                    </div>
                    <div className="col-md-6 text-right ellipsis clickable">
                        <button type="button" className="dropdown-toggle" data-toggle="dropdown"
                                aria-expanded="false">
                            <i className="far fa-ellipsis-v"/>
                        </button>
                        <ul className="dropdown-menu dropdown-menu--small" role="menu">
                            <li>
                                <Link to={`invoices/edit/${id}`}>Edit</Link>
                            </li>
                            <li>
                                <a href="#" className="invoice-delete-trigger" data-href={`invoices/delete/${id}`}>Delete</a>
                            </li>
                        </ul>
                    </div>
                    <div className="clearfix"/>
                    <div className="text-container">
                        <div className="col-md-7 amount">
                            <Link to={`invoices/edit/${id}`} className="clickable">
                                <h4>{currency}{outstanding.formatMoney()}</h4></Link>
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
                            {status === 'Paid' &&
                            <span><i className="far fa-calendar-alt"/>&nbsp;&nbsp;<span
                                style={{
                                    fontSize: 12,
                                    color: "#9A9A9A"
                                }}>Paid on {Util.getDateHuman(paid_date, true)}</span></span>
                            }
                            {status === 'Sent' &&
                            <span><i className="far fa-calendar-alt"/>&nbsp;&nbsp;<span
                                style={{
                                    fontSize: 12,
                                    color: "#9A9A9A"
                                }}>Sent on {Util.getDateHuman(sent_date, true)}</span></span>
                            }
                            {(status === 'PartiallyPaid' || status === 'Open') &&
                            <span><i className="far fa-calendar-alt"/>&nbsp;&nbsp;<span
                                style={{
                                    fontSize: 12,
                                    color: "#9A9A9A"
                                }}>Due on {Util.getDateHuman(due_date, true)}</span></span>
                            }
                        </div>
                        <div className="trash">
                            <button data-href={`invoices/delete/${id}`} className="invoice-delete-trigger clickable"><i
                                className="far fa-trash-alt"/></button>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}