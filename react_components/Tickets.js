import React, {Component} from 'react';
import StatusLabel from "./StatusLabel";
import Table from "./Table";
import EmptyContent from "./EmptyContent";
import * as Constants from "./Constants";
import HeaderTabs from "./HeaderTabs";
import API from './Api.js';
import {Redirect} from "react-router-dom";

const Table2 = ({data, onDelete, onFavourite, onRowClick}) => (
    <Table
        headings={[
            {
                text: "Ticket ID",
                attributes: {width: "70px", className: "hidden-xs"}
            },
            {
                text: "Status"
            },
            {
                text: "Subject",
                attributes: {className: "hidden-xs"}
            },
            {
                text: "Queue",
                attributes: {className: "hidden-xs"}
            },
            {
                text: "Client",
                attributes: {className: "hidden-xs"}
            },
            {
                text: "Owner"
            },
            {
                text: <div><i className="far fa-trash-alt"/>&nbsp;&nbsp;&nbsp;&nbsp;<i className="fa fa-star-o"/></div>,
                attributes: {
                    className: "no_sort text-right table-actions",
                    width: "70px",
                    style: {
                        color: "#C9C9C9",
                        paddingRight: "0px"
                    }
                }
            }
        ]}
        data={data}
        renderItem={({id, reference, status, subject, queue, client, user}, index) => (
            <tr id={id} key={id} onClick={(e) => onRowClick(e, id)}>
                <td className="hidden-xs">{reference}</td>
                <td><StatusLabel status={status}/></td>
                <td>{subject}</td>
                <td className="hidden-xs">{queue ? queue.name : ''}</td>
                <td className="hidden-xs"><StatusLabel status={client ? 'Open' : ''}
                                                       text={client ? `${client.firstname} ${client.lastname}` : 'No Client Assigned'}/>
                </td>
                <td><StatusLabel status={user ? 'Open' : ''}
                                 text={user ? `${user.firstname} ${user.lastname}` : 'No Owner'}/></td>
                <td className="table-actions">
                    <div><i onClick={(e) => onDelete(e, id)} className="far fa-trash-alt"/>&nbsp;&nbsp;&nbsp;&nbsp;<i
                        onClick={(e) => onFavourite(e, id)} className="fa fa-star-o"/></div>
                </td>
            </tr>
        )}/>
);

export default class Tickets extends Component {
    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {
            loading: true,
            tickets: [],
            redirect: false,
        }
    }

    componentWillMount() {
        this.load();
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    load() {
        API.get('tickets/data')
            .then(res => res.data)
            .then(data => this.initialize(data));
    }

    initialize(data) {
        this.updateState({
            tickets: data.tickets,
            loading: false
        }, () => {
            $(document).trigger('onComponentLoad');
        });
    }

    onFavourite(e, id) {

    }

    onDelete(e, id) {
        API.get(format('tickets/delete/{0}', id))
            .then(res => res.data)
            .then(data => this.onDeleteFinish(data));
    }

    onDeleteFinish(response) {
        const {status} = response;

        if (status) {
            this.load();
        }
    }

    onEdit(e, id) {
        this.updateState({
            redirect: `/tickets/edit/${id}`
        });
    }

    render() {
        const {tickets, loading, redirect} = this.state;

        if (redirect) {
            return <Redirect to={redirect} from="/projects"/>
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
                        {tickets.length > 0 &&
                        <div>
                            <div className="tabb-header">
                                <div className="col-md-6 table-header-left">
                                    <h2 className="page-title">Tickets</h2>
                                </div>
                                <div className="col-md-6 text-right table-header-right">
                                    <div>
                                        <a href="#" className="transparent"><i
                                            className={"far fa-search"}/></a>
                                    </div>
                                    <div>
                                        <a href="/tickets/create" data-toggle="mainmodal" className="btn btn-success">New Ticket</a>
                                    </div>
                                </div>
                            </div>
                            <div className="clearfix"/>
                            <div className="tabb-content">
                                <Table2 data={tickets}
                                        onRowClick={this.onEdit.bind(this)}
                                        onFavourite={this.onFavourite.bind(this)}
                                        onDelete={this.onDelete.bind(this)}/>
                            </div>
                        </div>
                        }
                        {tickets.length <= 0 &&
                        <EmptyContent
                            title="You haven’t created any ticket, yet!"
                            description="Create your first ticket."
                            button={
                                <button onClick={this.onAddNewClick.bind(this)} className="btn btn-danger">Add First
                                    Ticket</button>
                            }/>
                        }
                    </div>
                </div>
                }
            </div>
        );
    }
}