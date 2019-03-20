import React, {Component} from 'react';
import {Link, Redirect} from "react-router-dom";
import API from './Api.js';
import HeaderTabs from "./HeaderTabs";
import * as Constants from "./Constants";
import AddNewGrid from "./AddNewGrid";
import EmptyContent from "./EmptyContent";
import ExpenseGridItem from "./ExpenseGridItem";
import Modal from "./Modal";
import StatusLabel from "./StatusLabel";
import Table from "./Table";
import Notify from "./Notify";
import ExpenseForm from "./ExpenseForm";
import DeleteModal from "./DeleteModal";

const Table2 = ({data, onDelete, onEdit}) => (
    <Table
        headings={[
            {
                text: "ID",
                attributes: {width: "70px", className: "hidden-xs"}
            },
            {
                text: "Category"
            },
            {
                text: "Description",
                attributes: {className: "hidden-xs"}
            },
            {
                text: "Due Date",
                attributes: {className: "hidden-xs"}
            },
            {
                text: "Amount",
                attributes: {className: "hidden-xs"}
            },
            {
                text: "Status"
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
        renderItem={({id, category, description, date, status, value, currency}) => (
            <tr id={id} key={id}>
                <td className="hidden-xs">{id}</td>
                <td><StatusLabel status={status} text={category}/></td>
                <td className="hidden-xs"><span>{description}</span></td>
                <td className="hidden-xs">{date}</td>
                <td className="hidden-xs">{currency + (value.formatMoney())}</td>
                <td className={status}><StatusLabel status={status}/></td>
                <td className="table-actions">
                    <div><i data-href={`expenses/delete/${id}`}
                            className="far fa-trash-alt expense-delete-trigger"/>&nbsp;&nbsp;&nbsp;&nbsp;<i
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
                    <ExpenseGridItem key={props.id} {...props} />
                ))}
                <AddNewGrid onClick={onAddNewClick.bind(this)} icon="fal fa-plus-circle" text="Add an Expense"/>
            </div>
        </div>
    </div>
);

export default class Expenses extends Component {
    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {
            view: 'grid',
            loading: true,
            expenses: [],
            redirect: false,
            status: 'All',
            statuses: ['All', 'Paid', 'Unpaid'],
            visibleAddNewModal: false,
            visibleEditModal: false,
            forceModalClose: false,
            forceEditModalClose: false,
            notify: false,
            notifyType: '',
            notifyMessage: '',
            editing_id: null
        }
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

    getData(loading = true) {
        this.updateState({
            loading: loading
        });

        API.get('expenses/data')
            .then(res => res.data)
            .then(data => this.initialize(data))
            .catch(e => this.onRequestError(e));
    }

    initialize(data) {
        this.updateState({
            expenses: data.expenses,
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
            visibleAddNewModal: true
        });
    }

    onRequestError(e) {
        console.error(e);
    }

    onNewItemFormSubmitted(data) {
        if (data.status) {
            this.updateState({
                forceModalClose: true,
                forceEditModalClose: true
            });

            this.getData();

        } else {
            this.updateState({
                notify: true,
                notifyType: 'error',
                notifyMessage: 'Operation failed'
            });
        }
    }

    onDelete(e, id) {
        e.preventDefault();

        API.get(format('expenses/delete/{0}', id))
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
            visibleEditModal: true,
            editing_id: id
        });
    }

    render() {
        const {view, expenses, loading, redirect, statuses, status, visibleAddNewModal, forceModalClose, notify, notifyType, notifyMessage, visibleEditModal, forceEditModalClose, editing_id} = this.state;

        if (redirect) {
            return <Redirect to={redirect} from="/expenses"/>
        }

        return (
            <div>
                <HeaderTabs {...this.props} tabs={Constants.MoneyTabs}/>
                <DeleteModal onItemDelete={data => this.getData()} trigger=".expense-delete-trigger"
                             title="Delete Expense"
                             text="Are you sure to delete this expense?"/>
                {visibleAddNewModal &&
                <Modal title="New Expense"
                       forceClose={forceModalClose}
                       onRequestClose={() => this.updateState({visibleAddNewModal: false, forceModalClose: false})}>
                    <ExpenseForm
                        beforeSubmit={() => {
                        }}
                        onSubmitted={(d) => this.onNewItemFormSubmitted(d)}/>
                </Modal>
                }
                {visibleEditModal &&
                <Modal title="Edit Expense"
                       forceClose={forceEditModalClose}
                       onRequestClose={() => this.updateState({visibleEditModal: false, forceEditModalClose: false})}>
                    <ExpenseForm
                        id={editing_id}
                        beforeSubmit={() => {
                        }}
                        onSubmitted={(d) => this.onNewItemFormSubmitted(d)}/>
                </Modal>
                }
                {notify &&
                <Notify
                    onRequestClose={() => this.updateState({notify: false})}
                    type={notifyType}
                    message={notifyMessage}/>
                }
                {loading &&
                <div className="tab-loader"/>
                }
                {!loading &&
                <div className="col-sm-12  col-md-12 main">
                    <div className="row">
                        {expenses.length > 0 &&
                        <div>
                            <div className="tabb-header">
                                <div className="col-md-6 table-header-left">
                                    <h2 className="page-title">{status} Expenses</h2>
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
                                            Expense</a>
                                    </div>
                                </div>
                            </div>
                            <div className="clearfix"/>
                            <div className="tabb-content">
                                {this.state.view === 'list' &&
                                <Table2 data={expenses} onDelete={this.onDelete.bind(this)}
                                        onEdit={this.onEdit.bind(this)}/>
                                }
                                {this.state.view === 'grid' &&
                                <Grid data={expenses} onAddNewClick={this.onAddNewClick.bind(this)}/>
                                }
                            </div>
                        </div>
                        }
                        {expenses.length <= 0 &&
                        <EmptyContent
                            title="You haven’t tracked any expenses, yet!"
                            description={<span>Add your first expense so you can track everything in once<br/> place. Assign it to a team member and/or a project.</span>}
                            button={
                                <button onClick={this.onAddNewClick.bind(this)} className="btn btn-danger">Add First
                                    Expense</button>
                            }/>
                        }
                    </div>
                </div>
                }
            </div>
        );
    }
}