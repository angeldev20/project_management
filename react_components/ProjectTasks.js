import React, {Component} from 'react';
import HeaderTabs from "./HeaderTabs";
import * as Constants from "./Constants";
import {Redirect} from "react-router-dom";
import API from './Api.js';
import {arrayMove, SortableContainer, SortableElement, SortableHandle} from "react-sortable-hoc";
import TasksList from "./TasksList";
import DateLabel from "./DateLabel";
import WorkerAvatars from "./WorkerAvatars";
import Util from "./Util";
import Modal from "./Modal";
import MilestoneForm from "./MilestoneForm";
import TaskSection from "./TaskSection";
import NewTaskRow from "./NewTaskRow";
import EmptyContent from "./EmptyContent";

const DragHandle = SortableHandle(() => (
    <a href="#" className="handle"><i
        className="far fa-bars"/></a>
));

const Item = SortableElement(({id, name, total, completed, tasks, collapsed, project_id, onCollapse, onEditing, onSave, onReload, onTaskClick, onEditMain, onDeleteMain}) => {
    let div_id = `milestone-${id}`;

    return (
        <div id={div_id} className="shadow-box tasks-box">
            <div className="table-head">
                <div className="row">
                    <div className="col-md-6">
                        <div className="handle-container"><DragHandle /></div>
                        <div className="milestone-name">{name}</div>
                        <div className="milestone-status">{completed}/{total} Completed</div>
                    </div>
                    <div className="col-md-6 text-right actions">
                        <div>
                            <button type="button" className="dropdown-toggle transparent" data-toggle="dropdown"
                                    aria-expanded="false"><i className="far fa-ellipsis-h"/></button>
                            <ul className="dropdown-menu dropdown-menu--small" role="menu">
                                <li><a href="#" onClick={(e) => onEditMain(e, {id, name})}>Edit</a></li>
                                <li><a href="#" onClick={(e) => onDeleteMain(e, id)}>Delete</a></li>
                            </ul>
                        </div>
                        <div>
                            {collapsed &&
                            <button onClick={() => onCollapse(id, false)} className="transparent"><i
                                className="far fa-plus-circle"/></button>
                            }
                            {!collapsed &&
                            <button onClick={() => onCollapse(id, true)} className="transparent"><i
                                className="far fa-minus-circle"/></button>
                            }
                        </div>
                    </div>
                </div>
            </div>
            <div className="tasks">
                <NewTaskRow
                    task_id={id}
                    onEditing={onEditing}
                    onSave={onSave}/>
                <TasksList project_id={project_id} id={id} items={tasks} onReload={onReload} onTaskClick={onTaskClick}/>
            </div>
        </div>
    );
});

const MainList = SortableContainer(({project_id, items, onEditing, onSave, onCollapse, onReload, onTaskClick, onEditMain, onDeleteMain}) => {
    return (
        <div>
            {items.map((item, index) => (
                <Item key={`main-task-${item.id}`}
                      index={index}
                      collapsed={item.collapsed}
                      onEditing={onEditing}
                      onSave={onSave}
                      onCollapse={onCollapse}
                      onReload={onReload}
                      onTaskClick={onTaskClick}
                      onEditMain={onEditMain}
                      onDeleteMain={onDeleteMain}
                      project_id={project_id}
                      {...item} />
            ))}
        </div>
    );
});

export default class ProjectTasks extends Component {

    constructor(props) {
        super(props);

        const {id} = props.match.params;

        this.isUnmounted = false;
        this.state = {
            redirect: false,
            loading: true,
            id: id,
            milestones: [],
            visibleAddNewModal: false,
            visibleEditModal: false,
            forceModalClose: false,
            forceEditModalClose: false,
            view_task: null,
            editing_milestone: null
        };
    }

    componentWillMount() {
        this.getData();
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    getData() {
        const {id} = this.state;

        this.updateState({
            loading: true
        });

        API.get(`projects/get/${id}/milestones`)
            .then(res => res.data)
            .then(data => this.initialize(data));
    }

    initialize(data) {
        if (data.status) {
            let milestones = data.data;

            this.updateState({
                milestones,
                loading: false
            });
        }
    }

    onRequestError(e) {
        console.error(e);
    }

    saveSorting() {
        const {id, milestones} = this.state;

        const orderedItems = milestones.map(item => {
            return item.id;
        });

        let postData = new FormData();

        postData.set(app.token_name, app.token);
        postData.set('milestones', JSON.stringify(orderedItems));

        API.post(`projects/edit/${id}/milestones/order`, postData, {
            headers: {'Content-Type': 'multipart/form-data'}
        });
    }

    onSortEnd(index) {
        let {milestones} = this.state;
        const {oldIndex, newIndex} = index;

        this.updateState({
            milestones: arrayMove(milestones, oldIndex, newIndex),
        }, () => this.saveSorting());
    };

    onEditing(e) {
        if (e.target.value !== "") {
            $(e.target).parents('.single-task').addClass('editing');
        } else {
            $(e.target).parents('.single-task').removeClass('editing');
        }
    }

    onSave(milestone_id, name, date, workers) {
        const {id} = this.state;

        this.updateState({
            loading: true
        });

        let postData = new FormData();

        postData.set(app.token_name, app.token);
        postData.set('milestone_id', milestone_id);
        postData.set('project_id', id);
        postData.set('name', name);
        postData.set('due_date', date);
        postData.set('workers', workers);
        postData.set('status', 'open');

        API.post(`projects/tasks/${milestone_id}/create`, postData, {
            headers: {'Content-Type': 'multipart/form-data'}
        })
            .then(res => res.data)
            .then(data => this.getData());
    }

    onCollapse(id, collapse) {
        let {milestones} = this.state;

        milestones = milestones.map(t => {
            if (t.id === id) {
                t.collapsed = collapse;
            }
            return t;
        });

        if (collapse == true) {
            $(`#milestone-${id}`).find('.tasks').css("display", "none");
        } else {
            $(`#milestone-${id}`).find('.tasks').css("display", "block");
        }

        this.updateState({
            milestones
        });
    }

    onReload() {
        this.getData();
    }

    onNewItemFormSubmitted(data) {
        this.updateState({
            forceModalClose: true
        }, () => this.getData());
    }

    onAddNewClick(e) {
        e.preventDefault();

        this.updateState({
            visibleAddNewModal: true
        });
    }

    onTaskClick(item) {
        this.updateState({
            view_task: item
        });
    }

    onTaskDetailsClose() {
        this.updateState({
            view_task: null
        });
    }

    onWorkersUpdate(id, workers) {

    }

    onDateUpdate(id, date) {

    }

    onDeleteMain(e, _id) {
        e.preventDefault();

        const {id} = this.state;

        this.updateState({
            loading: true
        });

        let postData = new FormData();
        postData.set(app.token_name, app.token);
        postData.set('id', _id);

        API.post(`projects/edit/${id}/milestones/delete`, postData, {
            headers: {'Content-Type': 'multipart/form-data'}
        })
            .then(res => {
                this.onReload();
            });
    }

    onEditMain(e, data) {
        e.preventDefault();

        this.updateState({
            editing_milestone: data,
            visibleEditModal: true
        });
    }

    onEditItemFormSubmitted(data) {
        this.updateState({
            forceEditModalClose: true
        }, () => this.getData());
    }

    render() {
        const {
            id,
            redirect,
            loading,
            milestones,
            visibleAddNewModal,
            visibleEditModal,
            forceModalClose,
            forceEditModalClose,
            editing_milestone,
            view_task
        } = this.state;

        if (redirect) {
            return <Redirect to={redirect} from="/projects"/>
        }

        return (
            <div>
                <HeaderTabs {...this.props} tabs={Constants.ProjectTabs(id)}/>
                {visibleAddNewModal &&
                <Modal title="Add New List"
                       forceClose={forceModalClose}
                       onRequestClose={() => this.updateState({visibleAddNewModal: false, forceModalClose: false})}>
                    <MilestoneForm
                        project_id={id}
                        beforeSubmit={() => {
                        }}
                        onSubmitted={(d) => this.onNewItemFormSubmitted(d)}/>
                </Modal>
                }
                {visibleEditModal &&
                <Modal title="Edit"
                       forceClose={forceEditModalClose}
                       onRequestClose={() => this.updateState({visibleEditModal: false, forceEditModalClose: false})}>
                    <MilestoneForm
                        project_id={id}
                        data={editing_milestone}
                        beforeSubmit={() => {
                        }}
                        onSubmitted={(d) => this.onEditItemFormSubmitted(d)}/>
                </Modal>
                }
                {loading &&
                <div className="tab-loader"/>
                }
                {!loading &&
                <div>
                    <div className="col-sm-12 col-md-12 main">
                        <div className="row">
                            <div className="tabb-header">
                                <div className="col-md-6 table-header-left">
                                    <h2 className="page-title">All Tasks</h2>
                                </div>
                                <div className="col-md-6 text-right table-header-right">
                                    <div><a href="#" onClick={(e) => this.onAddNewClick(e)} className="btn btn-success">New
                                        List</a>
                                    </div>
                                </div>
                            </div>
                            <div className="clearfix"/>
                            <div className="tabb-content">
                                {milestones.length <= 0 &&
                                <EmptyContent
                                    title="No Tasks found!"
                                    description="Create first task"
                                    button={
                                        <button onClick={(e) => this.onAddNewClick(e)} className="btn btn-danger">
                                            Add
                                            First
                                            Task</button>
                                    }
                                />
                                }
                                <div className="row">
                                    <div className={view_task ? "col-sm-12 col-md-8" : "col-sm-12 col-md-12"}>
                                        <MainList items={milestones}
                                                  project_id={id}
                                                  onEditing={this.onEditing.bind(this)}
                                                  onSave={this.onSave.bind(this)}
                                                  onSortEnd={this.onSortEnd.bind(this)}
                                                  onCollapse={this.onCollapse.bind(this)}
                                                  onReload={this.onReload.bind(this)}
                                                  onTaskClick={this.onTaskClick.bind(this)}
                                                  onDeleteMain={this.onDeleteMain.bind(this)}
                                                  onEditMain={this.onEditMain.bind(this)}
                                                  useDragHandle={true}/>
                                    </div>
                                    {view_task &&
                                    <div className={"col-md-4"}>
                                        <TaskSection {...view_task}
                                                     loading={false}
                                                     onDateUpdate={this.onDateUpdate.bind(this)}
                                                     onWorkersUpdate={this.onWorkersUpdate.bind(this)}
                                                     onClose={this.onTaskDetailsClose.bind(this)}/>
                                    </div>
                                    }
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