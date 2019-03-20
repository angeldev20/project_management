import React, {Component} from 'react';
import {Link, Redirect} from "react-router-dom";
import API from './Api.js';
import InvoiceGridItem from "./InvoiceGridItem";
import HeaderTabs from "./HeaderTabs";
import * as Constants from "./Constants";
import format from 'string-format';
import AddNewGrid from "./AddNewGrid";
import EmptyContent from "./EmptyContent";
import EstimateGridItem from "./EstimateGridItem";
import Table from "./Table";
import StatusLabel from "./StatusLabel";
import Checkbox from "./Checkbox";
import Modal from "./Modal";
import EditInvoiceForm from "./EditInvoiceForm";
import Notify from "./Notify";
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
        renderItem={({id, reference, company, issue_date, estimate_status, due_date, sum, currency, sent_date}, index, hasCheckbox) => (
            <tr id={id} key={id}>
                {hasCheckbox &&
                <td className="text-center">
                    <Checkbox checkboxAttributes={{name: "checks[]", value: id, className: "checks"}}/>
                </td>
                }
                <td className="hidden-xs">{reference}</td>
                <td><StatusLabel status={estimate_status} text={company ? company.name : ''}/></td>
                <td className="hidden-xs"><span>{issue_date}</span></td>
                <td className="hidden-xs"><StatusLabel status={estimate_status} text={due_date}/></td>
                <td className="hidden-xs">{currency + (sum.formatMoney())}</td>
                <td className={estimate_status}><StatusLabel status={estimate_status}/></td>
                <td className="table-actions">
                    <div><i data-href={`invoices/delete/${id}`}
                            className="far fa-trash-alt estimate-delete-trigger"/>&nbsp;&nbsp;&nbsp;&nbsp;<i
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
                    <EstimateGridItem onClick={onClick} key={props.id} {...props} />
                ))}
                <AddNewGrid onClick={onAddNewClick.bind(this)} icon="fal fa-plus-circle" text="Add an Estimate"/>
            </div>
        </div>
    </div>
);

export default class Estimates extends Component {
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
        API.get('estimates/data')
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

        API.get(`estimates/data?status=${status}`)
            .then(res => res.data)
            .then(data => this.initialize(data))
            .catch(e => this.onRequestError(e));
    }

    onGridItemClick({id}) {
        this.updateState({
            redirect: `/estimates/edit/${id}`
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
        formData.set('estimate_status', 'Draft');
        formData.set('currency', '$');

        API.post("estimates/store", formData, {
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
                redirect: `/estimates/edit/${invoice_id}`
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
            redirect: `estimates/edit/${id}`
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
                notifyMessage: 'Failed to edit estimates'
            });
        }
    }

    render() {
        const {view, invoices, loading, redirect, statuses, status, checks, visibleEditModal, forceModalClose, notify, notifyType, notifyMessage} = this.state;

        if (redirect) {
            return <Redirect to={redirect} from="/estimates"/>
        }

        return (
            <div>
                <HeaderTabs {...this.props} tabs={Constants.MoneyTabs}/>
                <DeleteModal onItemDelete={data => this.reloadInvoices()} trigger=".estimate-delete-trigger"
                             title="Delete Estimate"
                             text="Are you sure to delete this estimate?"/>
                {visibleEditModal &&
                <Modal title="Edit Estimates"
                       forceClose={forceModalClose}
                       onRequestClose={() => this.updateState({visibleEditModal: false, forceModalClose: false})}>
                    <EditInvoiceForm
                        ids={checks}
                        isEstimate={true}
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
                                    <h2 className="page-title">{status} Estimates&nbsp;
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
                                                               }}>{`${s} Estimates`}</a>
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
                                        <a href="#" onClick={(e) => this.onAddNewClick(e)} className="btn btn-success">New
                                            Estimate
                                        </a>
                                    </div>
                                    {checks.length > 0 &&
                                    <div>
                                        <a href="#" onClick={(e) => this.onEditClick(e)} className="btn btn-success">Edit
                                            Estimates
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
                            title="You haven’t any estimates, yet!"
                            description="Add your first estimate."
                            button={
                                <button onClick={this.onAddNewClick.bind(this)} className="btn btn-danger">Add First
                                    Estimate</button>
                            }/>
                        }
                    </div>
                </div>
                }
            </div>
        );
    }
}