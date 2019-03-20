import React, {Component} from 'react';
import {Link, Redirect} from "react-router-dom";
import API from './Api.js';
import HeaderTabs from "./HeaderTabs";
import * as Constants from "./Constants";
import AddNewGrid from "./AddNewGrid";
import EmptyContent from "./EmptyContent";
import LeadGridItem from "./LeadGridItem";
import Table from "./Table";

const Table2 = ({data, onDelete, onEdit}) => (
    <Table
        headings={[
            {
                text: "ID",
                attributes: {width: "70px", className: "hidden-xs"}
            },
            {
                text: "Name"
            },
            {
                text: "Leads",
                attributes: {className: "hidden-xs"}
            },
            {
                text: <div><i className="far fa-trash-alt"/>&nbsp;&nbsp;&nbsp;&nbsp;<i className="far fa-edit"/></div>,
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
        renderItem={({id, name, leads}) => (
            <tr id={id} key={id}>
                <td className="hidden-xs">{id}</td>
                <td>{name}</td>
                <td className="hidden-xs">{leads}</td>
                <td className="table-actions">
                    <div><i onClick={(e) => onDelete(e, id)} className="far fa-trash-alt"/>&nbsp;&nbsp;&nbsp;&nbsp;<i
                        onClick={(e) => onEdit(e, id)} className="far fa-edit"/></div>
                </td>
            </tr>
        )}/>
);

const Grid = ({data, onAddNewClick}) => (
    <div className="grids">
        <div className="row">
            <div className="grid grid--align-content-start">
                {data.map((props) => (
                    <LeadGridItem key={props.id} {...props} />
                ))}
                <AddNewGrid onClick={onAddNewClick.bind(this)} icon="fal fa-plus-circle" text="Add new lead"/>
            </div>
        </div>
    </div>
);

export default class Leads extends Component {
    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {
            view: 'grid',
            loading: true,
            leads: [],
            redirect: false
        };
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    componentWillMount() {
        this.getData();
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    getData() {
        API.get('leads/data')
            .then(res => res.data)
            .then(data => this.initialize(data))
            .catch(e => this.onRequestError(e));
    }

    initialize(data) {
        this.updateState({
            leads: data.leads,
            loading: false
        }, () => {
            $(document).trigger('onComponentLoad');
        });
    }

    changeView(e, view) {
        e.preventDefault();

        this.updateState({
            view
        }, () => {
            $(document).trigger('onComponentLoad');
        });
    }

    onAddNewClick(e) {
        e.preventDefault();

        this.updateState({
            redirect: '/leads/create'
        });
    }

    onRequestError(e) {
        console.error(e);
    }

    onDelete(e, id) {
        e.preventDefault();

        API.get(format('leads/delete/{0}', id))
            .then(res => res.data)
            .then(data => this.onDeleteFinish(data));
    }

    onDeleteFinish(response) {
        const {status} = response;

        if (status) {
            this.getData();
        }
    }

    onEdit(e, id) {
        e.preventDefault();

        this.updateState({
            redirect: `/leads/edit/${id}`
        });
    }

    render() {
        const {view, leads, loading, redirect} = this.state;

        if (redirect) {
            return <Redirect to={redirect} from="/leads"/>
        }

        return (
            <div>
                <HeaderTabs {...this.props} tabs={Constants.MoneyTabs}/>
                {loading &&
                <div className="tab-loader"/>
                }
                {!loading &&
                <div className="col-sm-12  col-md-12 main">
                    <div className="row">
                        {leads.length > 0 &&
                        <div>
                            <div className="tabb-header">
                                <div className="col-md-6 table-header-left">
                                    <h2 className="page-title">All Leads</h2>
                                </div>
                                <div className="col-md-6 text-right table-header-right">
                                    <div>
                                        <a href="#" className="transparent"><i
                                            className={"far fa-search"}/></a>
                                    </div>
                                    <div>
                                        <a href="#" className={'transparent ' + (view === 'grid' ? 'active' : '')}
                                           onClick={(e) => this.changeView(e, 'grid')}><i
                                            className={"far fa-th"}/></a>
                                    </div>
                                    <div>
                                        <a href="#" className={'transparent ' + (view === 'list' ? 'active' : '')}
                                           onClick={(e) => this.changeView(e, 'list')}><i
                                            className={"far fa-th-list"}/></a>
                                    </div>
                                    <div>
                                        <a href="#" onClick={this.onAddNewClick.bind(this)} className="btn btn-success">New
                                            Item</a>
                                    </div>
                                </div>
                            </div>
                            <div className="clearfix"/>
                            <div className="tabb-content">
                                {this.state.view === 'list' &&
                                <Table2 data={leads} onEdit={this.onEdit.bind(this)}
                                        onDelete={this.onDelete.bind(this)}/>
                                }
                                {this.state.view === 'grid' &&
                                <Grid data={leads} onAddNewClick={this.onAddNewClick.bind(this)}/>
                                }
                            </div>
                        </div>
                        }
                        {leads.length <= 0 &&
                        <EmptyContent
                            title="No leads, yet!"
                            description="Add your first lead."
                            button={
                                <button onClick={this.onAddNewClick.bind(this)} className="btn btn-danger">Add First
                                    Lead</button>
                            }/>
                        }
                    </div>
                </div>
                }
            </div>
        );
    }
}