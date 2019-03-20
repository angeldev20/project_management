import React, {Component} from 'react';
import {Link, Redirect} from "react-router-dom";
import API from './Api.js';
import HeaderTabs from "./HeaderTabs";
import * as Constants from "./Constants";
import EmptyContent from "./EmptyContent";
import {SortableContainer, SortableElement, SortableHandle, arrayMove} from 'react-sortable-hoc';
import DetailedRowItem from "./DetailedRowItem";
import StatusLabel from "./StatusLabel";
import Comments from "./Comments";
import CommentItem from "./CommentItem";
import CommentBox from "./CommentBox";
import Util from "./Util";

const Heading = ({id}) => (
    <h2 className="page-title">{id ? `Ticket #${id}` : 'New Ticket'}</h2>
);

const Detail = ({label, value}) => {
    return (
        <div className="detail-row">
            <div className="col-md-6">
                <div className="table-container">
                    <div className="table-cell">{label}</div>
                </div>
            </div>
            <div className="col-md-6 text-right">
                <span>{value}</span>
            </div>
        </div>
    );
};

export default class EditTicket extends Component {
    constructor(props) {
        super(props);

        const {id} = props.match.params;

        this.isUnmounted = false;
        this.state = {
            redirect: false,
            loading: true,
            id: id,
            customers: [],
            types: [],
            users: [],
            commentSubmitting: false
        };
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    componentWillMount() {
        const {id} = this.state;

        API.get(`tickets/details/${id}`)
            .then(res => res.data)
            .then(data => this.initialize(data))
            .catch(e => this.onRequestError(e));

        API.get(`clients/data`)
            .then(res => res.data)
            .then(data => this.customersFetched(data));

        API.get(`users/data`)
            .then(res => res.data)
            .then(data => this.usersFetched(data));

        API.get(`types/data`)
            .then(res => res.data)
            .then(data => this.typesFetched(data));
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    onRequestError(e) {
        console.error(e);
    }

    initialize(data) {
        if (data.status) {
            data.data['loading'] = false;

            this.updateState(data.data);
        }
    }

    customersFetched(data) {
        if (data.status) {
            let customers = data.data.map(customer => {
                return {value: customer.id, text: customer.firstname};
            });

            this.updateState({customers});
        }
    }

    typesFetched(data) {
        if (data.status) {
            let types = data.data.map(type => {
                return {value: type.id, text: type.name};
            });

            this.updateState({types});
        }
    }

    usersFetched(data) {
        if (data.status) {
            let users = data.data.map(user => {
                return {value: user.id, text: user.firstname};
            });

            this.updateState({users});
        }
    }

    updateFieldValue(e) {
        let field = {};

        field[e.target.name] = e.target.value;

        this.updateState(field);
    }

    updateInvoice(postData) {
        const {id} = this.state;

        postData.set(app.token_name, app.token);

        API.post(`ticket/update/${id}`, postData, {
            headers: {'Content-Type': 'multipart/form-data'}
        })
            .then(res => res.data)
            .then(data => this.ticketUpdated(data));
    }

    ticketUpdated(data) {

    }

    updateInvoiceField(key, value) {
        let postData = new FormData();

        postData.set(key, value);

        this.updateInvoice(postData);
    }

    onPostComment(comment) {
        const {id, articles} = this.state;

        let postData = new FormData();

        postData.set(app.token_name, app.token);
        postData.set('message', comment);

        API.post(`tickets/comment/${id}`, postData, {
            headers: {'Content-Type': 'multipart/form-data'}
        })
            .then(res => res.data)
            .then(data => {
                if (data.status) {
                    articles.push(data.article);
                    this.updateState({
                        articles,
                        commentSubmitting: false
                    });
                }
            });
    }

    render() {
        const {
            redirect,
            loading,
            id,
            created,
            reference,
            status,
            type,
            from,
            client,
            user,
            articles,
            client_id,
            customers,
            users,
            types,
            type_id,
            user_id,
            text,
            commentSubmitting
        } = this.state;

        const statuses = [
            {value: 'Draft', text: 'Draft'},
            {value: 'Sent', text: 'Sent'},
            {value: 'Paid', text: 'Paid'},
            {value: 'Overdue', text: 'Overdue'},
        ];

        let from_name = '';

        if (from) {
            from_name = from.split('-');
            from_name = from_name[0];
        }

        if (redirect) {
            return <Redirect to={redirect} from="/tickets"/>
        }

        return (
            <div>
                <HeaderTabs {...this.props} tabs={Constants.WorkTabs}/>
                {loading &&
                <div className="tab-loader"/>
                }
                {!loading &&
                <div className="col-sm-12  col-md-12 main">
                    <div className="row">
                        <div className="tabb-header">
                            <div className="col-md-6 table-header-left">
                                <Heading id={id}/>
                            </div>
                            <div className="col-md-6 text-right table-header-right">
                                <div>
                                    <a href="/tickets/create" data-toggle="mainmodal" className="btn btn-success">New
                                        Ticket</a>
                                </div>
                            </div>
                        </div>
                        <div className="clearfix"/>
                        <div className="tabb-content">
                            <div className="row">
                                <div id="invoice-detailed-section" className="col-md-3">
                                    <div className="shadow-box">
                                        <div className="table-head">Details</div>
                                        <div className="invoice-details">
                                            <Detail label="Created"
                                                    value={created ? Util.getDateHuman(created, true) : ''}/>
                                            <Detail label="Status" value={<StatusLabel status={status}/>}/>
                                            <Detail label="Type"
                                                    value={type ? <StatusLabel status="open" text={type.name}/> : ''}/>
                                            <Detail label="From" value={from_name}/>
                                            <Detail label="Customer" value={client ?
                                                <StatusLabel status="sent" text={client.firstname}/> : ''}/>
                                            <Detail label="Owner" value={user ? user.firstname : ''}/>
                                            <div className="clearfix"/>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-md-9">
                                    <div className="shadow-box">
                                        <div className="table-head">{text}</div>
                                        <Comments data={articles}
                                                  renderItem={(item, index) => <CommentItem key={index} {...item}/>}/>
                                        <CommentBox submitting={commentSubmitting}
                                                    onSubmit={this.onPostComment.bind(this)}/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                }
            </div>
        );
    }
}