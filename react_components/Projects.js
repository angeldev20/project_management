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
import Grid from "./Grid";
import ProjectGridItem from "./ProjectGridItem";
import Util from "./Util";
import DeleteModal from "./DeleteModal";

const Table2 = ({data, onDelete, onEdit}) => (
    <Table
        headings={[
            
            {
                text: "Name"
            },
            {
                text: "Client",
                attributes: {className: "hidden-xs"}
            },
            {
                text: "Deadline",
                attributes: {className: "hidden-xs"}
            },
            {
                text: "Category",
                attributes: {className: "hidden-xs"}
            },
            {
                text: "Assigned"
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
        renderItem={({id, reference, name, company, end, category, assigned_ids}, index) => (
            <tr id={id} key={id}>
                
                <td>{name}</td>
                <td><StatusLabel status="Paid" text={company ? company.name : ''}/></td>
                <td className="hidden-xs">{Util.getSlashedDate(end)}</td>
                <td className="hidden-xs">{category}</td>
                <td>assigned</td>
                <td className="table-actions">
                    <div><i data-href={`projects/delete/${id}`}
                            className="far fa-trash-alt project-delete-trigger"/>&nbsp;&nbsp;&nbsp;&nbsp;<a
                        href={`/projects/update/${id}`} data-toggle="mainmodal"><i
                        className="far fa-edit"/></a></div>
                </td>
            </tr>
        )}/>
);

export default class Projects extends Component {
    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {
            view: 'grid',
            loading: true,
            projects: [],
            redirect: false,
            status: 'All',
            statuses: ['All', 'Open', 'Closed'],
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
        API.get('projects/data')
            .then(res => res.data)
            .then(data => this.initialize(data));
    }

    componentDidMount() {

    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    initialize(data) {
        this.updateState({
            projects: data.projects,
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

    reloadProjects(status = 'All') {
        this.updateState({
            loading: true,
            status: status
        });

        API.get(`projects/data?status=${status}`)
            .then(res => res.data)
            .then(data => this.initialize(data))
            .catch(e => this.onRequestError(e));
    }

    onGridItemClick({id}) {
        window.location.href = `/projects/view/${id}/tasks`;
    }

    onAddNewClick(e) {
        e.preventDefault();

        $("#add-new-project").trigger("click");
    }

    onNewItemAddFailed(e) {
        this.creatingProject = false;
        this.onRequestError(e);
    }

    onRequestError(e) {
        console.error(e);
    }

    onDelete(e, id) {
        e.preventDefault();

        API.get(format('projects/delete/{0}', id))
            .then(res => res.data)
            .then(data => this.onDeleteFinish(data));
    }

    onDeleteFinish(response) {
        const {status} = response;

        if (status) {
            this.reloadProjects(status);
        } else {
            this.updateState({
                notify: true,
                notifyType: 'error',
                notifyMessage: 'Failed to delete project'
            });
        }
    }

    onEdit(e, id) {
        e.preventDefault();

        this.updateState({
            redirect: `projects/view/${id}/tasks`
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

            this.reloadProjects(this.state.status);

        } else {
            this.updateState({
                notify: true,
                notifyType: 'error',
                notifyMessage: 'Failed to edit projects'
            });
        }
    }

    render() {
        const {view, projects, loading, redirect, statuses, status, checks, visibleEditModal, forceModalClose, notify, notifyType, notifyMessage} = this.state;

        if (redirect) {
            return <Redirect to={redirect} from="/projects"/>
        }

        return (
            <div>
                <HeaderTabs {...this.props} tabs={Constants.WorkTabs}/>
                <DeleteModal onItemDelete={data => this.onDeleteFinish(data)} trigger=".project-delete-trigger"
                             title="Delete Project"
                             text="Are you sure to delete this project?"/>
                {visibleEditModal &&
                <Modal title="Edit Projects"
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
                        {projects.length > 0 &&
                        <div>
                            <div className="tabb-header">
                                <div className="col-md-6 table-header-left">
                                    <h2 className="page-title">{status} Projects&nbsp;
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
                                                                   this.reloadProjects(s);
                                                               }}>{`${s} Projects`}</a>
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
                                        <a href="/projects/create" data-toggle="mainmodal" id="add-new-project"
                                           className="btn btn-success">New
                                            Project
                                        </a>
                                    </div>
                                    {checks.length > 0 &&
                                    <div>
                                        <a href="#" onClick={(e) => this.onEditClick(e)} className="btn btn-success">Edit
                                            Projects
                                        </a>
                                    </div>
                                    }
                                </div>
                            </div>
                            <div className="clearfix"/>
                            <div className="tabb-content">
                                {this.state.view === 'list' &&
                                <Table2 data={projects} onEdit={this.onEdit.bind(this)}
                                        onDelete={this.onDelete.bind(this)}/>
                                }
                                {this.state.view === 'grid' &&
                                <Grid GridItem={(props) => (
                                    <ProjectGridItem
                                        onClick={this.onGridItemClick.bind(this)}
                                        {...props}/>
                                )}
                                      AddNewItem={(props) => <AddNewGrid {...props}/>}
                                      AddNewItemText="Add New Project"
                                      data={projects}
                                      onAddNewClick={this.onAddNewClick.bind(this)}/>
                                }
                            </div>
                        </div>
                        }
                        {projects.length <= 0 &&
                        <EmptyContent
                            title="No Project found!"
                            description="Create your first project."
                            button={
                                <button onClick={this.onAddNewClick.bind(this)} className="btn btn-danger">Add
                                    Project</button>
                            }/>
                        }
                        <div className="hide">
                            <a href="/projects/create" data-toggle="mainmodal" id="add-new-project" className="btn btn-success"></a>
                        </div>
                    </div>
                </div>
                }
            </div>
        );
    }
}