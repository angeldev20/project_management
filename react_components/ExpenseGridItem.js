import React, {Component} from 'react';
import API from './Api.js';
import format from 'string-format';
import StatusLabel from "./StatusLabel";
import Util from "./Util";

export default class ExpenseGridItem extends Component {

    constructor(props) {
        super(props);

        this.id = "expense-box-" + this.props.id;
        this.state = {
            loading: false,
            isDeleted: false
        };
    }

    onDelete(id) {
        this.setState({loading: true});

        API.get(format('expenses/delete/{0}', id))
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
        const {id, reference, category, status, value, currency, description, date} = this.props;
        const {loading} = this.state;

        return (
            <div
                id={this.id}
                className={"grid-box-container grid__col-xs-6 grid__col-sm-6 grid__col-md-6 grid__col-lg-3" + (loading ? " loading" : "") }>
                <div className="grid-box invoice-box">
                    <div className="col-md-6 status">
                        <StatusLabel status={status}/>
                    </div>
                    <div className="col-md-6 text-right ellipsis">
                        <button type="button" className="dropdown-toggle" data-toggle="dropdown"
                                aria-expanded="false">
                            <i className="far fa-ellipsis-v"/>
                        </button>
                        <ul className="dropdown-menu dropdown-menu--small" role="menu">
                            <li>
                                <a href="#" className="expense-delete-trigger" data-href={`expenses/delete/${id}`}>Delete</a>
                            </li>
                        </ul>
                    </div>
                    <div className="clearfix"/>
                    <div className="text-container">
                        <div className="col-md-7 amount">
                            <h4>{currency}{value.formatMoney()}</h4>
                        </div>
                        <div className="col-md-5 text-right reference">
                            <span>{reference ? `#${reference}` : ''}</span>
                        </div>
                        <div className="clearfix"/>
                        <div className="col-md-12 company">
                            <h5>{category}</h5>
                        </div>
                        <div className="clearfix"/>
                        <div className="col-md-12 description">{description}</div>
                    </div>
                    <div className="grid-footer">
                        <div className="datetime">
                            <span><i className="far fa-calendar-alt"/>&nbsp;&nbsp;<span
                                style={{
                                    fontSize: 12,
                                    color: "#9A9A9A"
                                }}>{status} on {Util.getDateHuman(date, true)}</span></span>
                        </div>
                        <div className="trash">
                            <button data-href={`expenses/delete/${id}`} className="expense-delete-trigger"><i
                                className="far fa-trash-alt"/></button>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}