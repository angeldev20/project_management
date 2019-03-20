import React, {Component} from 'react';
import {Link, Redirect} from "react-router-dom";
import API from './Api.js';
import InvoiceGridItem from "./InvoiceGridItem";
import HeaderTabs from "./HeaderTabs";
import * as Constants from "./Constants";
import format from 'string-format';
import AddNewGrid from "./AddNewGrid";
import EmptyContent from "./EmptyContent";
import StatusLabel from "./StatusLabel";
import Table from "./Table";
import Checkbox from "./Checkbox";
import Modal from "./Modal";
import Notify from "./Notify";
import EditInvoiceForm from "./EditInvoiceForm";
import Util from "./Util";
import DeleteModal from "./DeleteModal";

const Table2 = ({data, onDelete, onEdit}) => (
    <Table
        headings={[
            {
                text: "Invoice ID",
                attributes: {width: "70px", className: "hidden-xs"}
            },
            {
                text: "Client"
            },
            {
                text: "Issue Date",
                attributes: {className: "hidden-xs"}
            },
            {
                text: "Due Date",
                attributes: {className: "hidden-xs"}
            },
            {
                text: "Value",
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
        showCheckboxes={true}
        renderItem={({id, reference, company, issue_date, status, due_date, sum, currency, sent_date}, index, hasCheckbox, onRowClick) => (
            <tr id={id} key={id} onClick={onRowClick.bind(this)}>
                {hasCheckbox &&
                <td className="text-center">
                    <Checkbox checkboxAttributes={{name: "checks[]", value: id, className: "checks"}}/>
                </td>
                }
                <td className="hidden-xs">{reference}</td>
                <td><StatusLabel status={status} text={company ? company.name : ''}/></td>
                <td className="hidden-xs">{issue_date ? Util.getSlashedDate(issue_date) : '-'}</td>
                <td className="hidden-xs">{due_date ? Util.getSlashedDate(due_date) : '-'}</td>
                <td className="hidden-xs">{currency + (sum.formatMoney())}</td>
                <td className={status}><StatusLabel status={status}/></td>
                <td className="table-actions">
                    <div><i data-href={`invoices/delete/${id}`}
                            className="far fa-trash-alt invoice-delete-trigger"/>&nbsp;&nbsp;&nbsp;&nbsp;<i
                        onClick={(e) => onEdit(e, id)} className="far fa-edit"/></div>
                </td>
            </tr>
        )}/>
);

const Grid = ({data, onAddNewClick, onClick}) => (
    <div className="grids">
        <div className="row">
            <div className="grid grid--align-content-start">
                {data.map((props) => (
                    <InvoiceGridItem onClick={onClick} key={props.id} {...props} />
                ))}
                <AddNewGrid onClick={onAddNewClick.bind(this)} icon="fal fa-plus-circle" text="Add an Invoice"/>
            </div>
        </div>
    </div>
);

export default class Invoices extends Component {
    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {
            view: 'grid',
            loading: true,
            invoices: [],
            redirect: false,
            status: 'All',
            statuses: ['All', 'Paid', 'Unpaid'],
            checks: [],
            visibleEditModal: false,
            forceModalClose: false,
            notify: false,
            notifyType: '',
            notifyMessage: ''
        };
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    componentWillMount() {
        API.get('invoices/data')
            .then(res => res.data)
            .then(data => this.initialize(data));
    }

    componentDidMount() {
        $(document).on("change", "input.checks", () => {
            var vals = [];
            $("input.checks:checked").each(function () {
                vals.push($(this).val());
            });
            this.updateState({
                checks: vals
            });
        });
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    initialize(data) {
        this.updateState({
            invoices: data.invoices,
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

    reloadInvoices(status = 'All') {
        this.updateState({
            loading: true,
            status: status
        });

        API.get(`invoices/data?status=${status}`)
            .then(res => res.data)
            .then(data => this.initialize(data))
            .catch(e => this.onRequestError(e));
    }

    onGridItemClick({id}) {
        this.updateState({
            redirect: `/invoices/edit/${id}`
        });
    }

    onAddNewClick(e) {
        
        e.preventDefault();

        let formData = new FormData();

        if (this.creatingInvoice)
            return;

        this.updateState({
            loading: true
        });

        this.creatingInvoice = true;

        formData.set(app.token_name, app.token);
        formData.set('status', 'Draft');
        formData.set('currency', '$');

        API.post("invoices/store", formData, {
            headers: {'Content-Type': 'multipart/form-data'}
        })
            .then(res => res.data)
            .then(data => this.onNewItemAdded(data))
            .catch(e => this.onNewItemAddFailed(e));
    }

    onNewItemAddFailed(e) {
        this.creatingInvoice = false;
        this.onRequestError(e);
    }

    onRequestError(e) {
        console.error(e);
    }

    onNewItemAdded(data) {
        this.creatingInvoice = false;

        if (data.status) {
            let invoice_id = data.data.id;

            this.updateState({
                redirect: `/invoices/edit/${invoice_id}`
            });
        }
    }

    onDelete(e, id) {
        e.preventDefault();

        API.get(format('invoices/delete/{0}', id))
            .then(res => res.data)
            .then(data => this.onDeleteFinish(data));
    }

    onDeleteFinish(response) {
        const {status} = response;

        if (status) {
            this.reloadInvoices(status);
        }
    }

    onEdit(e, id) {
        e.preventDefault();

        this.updateState({
            redirect: `/invoices/edit/${id}`
        });
    }

    onEditClick(e) {
        e.preventDefault();

        this.updateState({
            visibleEditModal: true
        });
    }

    onEditFormSubmitted(data) {
        if (data.status) {
            this.updateState({
                forceModalClose: true
            });

            this.reloadInvoices(this.state.status);

        } else {
            this.updateState({
                notify: true,
                notifyType: 'error',
                notifyMessage: 'Failed to edit invoices'
            });
        }
    }

    render() {
        const {view, invoices, loading, redirect, statuses, status, checks, visibleEditModal, forceModalClose, notify, notifyType, notifyMessage} = this.state;

        if (redirect) {
            return <Redirect to={redirect} from="/invoices"/>
        }

        return (
            <div>
                <HeaderTabs {...this.props} tabs={Constants.MoneyTabs}/>
                <DeleteModal onItemDelete={data => this.reloadInvoices()} trigger=".invoice-delete-trigger"
                             title="Delete Invoice"
                             text="Are you sure to delete this invoice?"/>
                {visibleEditModal &&
                <Modal title="Edit Invoices"
                       forceClose={forceModalClose}
                       onRequestClose={() => this.updateState({visibleEditModal: false, forceModalClose: false})}>
                    <EditInvoiceForm
                        ids={checks}
                        beforeSubmit={() => {
                        }}
                        onSubmitted={(d) => this.onEditFormSubmitted(d)}/>
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
                        {invoices.length > 0 &&
                        <div>
                            <div className="tabb-header">
                                <div className="col-md-6 table-header-left">
                                    <h2 className="page-title">{status} Invoices&nbsp;
                                        <button type="button" className="dropdown-toggle transparent"
                                                data-toggle="dropdown"
                                                aria-expanded="false">
                                            <i className="far fa-angle-down"/>
                                        </button>
                                        <ul className="dropdown-menu dropdown-menu--small" role="menu">
                                            {statuses.map((s) => (
                                                <li key={s}><a href="#"
                                                               onClick={(e) => {
                                                                   e.preventDefault();
                                                                   this.reloadInvoices(s);
                                                               }}>{`${s} Invoices`}</a>
                                                </li>
                                            ))}
                                        </ul>
                                    </h2>
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
                                        <a  onClick={(e) => this.onAddNewClick(e)} className="btn btn-success">New Invoice
                                        </a>
                                    </div>
                                    {checks.length > 0 &&
                                    <div>
                                        <a href="#" onClick={(e) => this.onEditClick(e)} className="btn btn-success">Edit
                                            Invoices
                                        </a>
                                    </div>
                                    }
                                </div>
                            </div>
                            <div className="clearfix"/>
                            <div className="tabb-content">
                                {this.state.view === 'list' &&
                                <Table2 data={invoices} onEdit={this.onEdit.bind(this)}
                                        onDelete={this.onDelete.bind(this)}/>
                                }
                                {this.state.view === 'grid' &&
                                <Grid data={invoices}
                                      onClick={this.onGridItemClick.bind(this)}
                                      onAddNewClick={this.onAddNewClick.bind(this)}/>
                                }
                            </div>
                        </div>
                        }
                        {invoices.length <= 0 &&
                        <EmptyContent
                            title="You haven’t created any invoice, yet!"
                            description="Create your first invoice."
                            button={
                                <button onClick={this.onAddNewClick.bind(this)} className="btn btn-danger">Add First
                                    Invoice</button>
                            }/>
                        }
                    </div>
                </div>
                }
            </div>
        );
    }
}