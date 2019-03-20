import React, {Component} from 'react';
import {arrayMove, SortableContainer, SortableElement, SortableHandle} from "react-sortable-hoc";
import WorkerAvatars from "./WorkerAvatars";
import API from './Api.js';
import DateLabel from "./DateLabel";
import Util from "./Util";
import Modal from "./Modal";
import AssignWorkerForm from "./AssignWorkerForm";

const DragHandle = SortableHandle(() => (
    <a href="#" className="handle"><i
        className="far fa-bars"/></a>
));

const Item = SortableElement(({id, workers, subtasks, comments, name, status, due_date, timertime, state, tracking, project_id, updateStatus, updateDate, onAddNewClick, onTaskClick, onEdit, onDelete}) => {
    return (
        <div className={`single-task status-${status}`} onClick={(e) => {
            var $hasClass = $(e.target).hasClass('clickable');
            var $hasParentClickable = $(e.target).parents('.clickable').length > 0;

            if (!$hasClass && !$hasParentClickable) {
                onTaskClick({
                    id,
                    workers: workers.map(w => w.worker),
                    subtasks,
                    comments,
                    name,
                    status,
                    due_date,
                    timertime,
                    state,
                    tracking
                });
            }
        }}>
            <div className="col-md-4">
                <div className="handle-container clickable"><DragHandle/></div>
                <div className="task-check clickable">{status === 'done' &&
                <i className="fa fa-check-circle" onClick={() => updateStatus(id, 'open')}/>
                }{status !== 'done' &&
                <i className="far fa-circle" onClick={() => updateStatus(id, 'done')}/>
                }</div>
                <div className="task-name">{name}</div>
            </div>
            <div className="col-md-4">
                <div className="clickable">
                    <DateLabel onChange={(selectedDates, dateStr, instance) => {
                        updateDate(id, selectedDates);
                    }} updateAble={true} date={due_date}/>
                </div>
            </div>
            <div className="col-md-4 text-right actions">
                <div className="clickable">
                    <WorkerAvatars workers={workers.map(w => w.worker)} allowAdd={true} limit={3}
                                   onAddNewClick={(e) => onAddNewClick(e, id)}/>
                </div>
                <div className="clickable">
                    <button type="button" className="dropdown-toggle transparent clickable" data-toggle="dropdown"
                            aria-expanded="false"><i className="far fa-ellipsis-h"/></button>
                    <ul className="dropdown-menu dropdown-menu--small" role="menu">
                        <li><a href={`/projects/task/${project_id}/update/${id}`} data-toggle="mainmodal">Edit</a></li>
                        <li><a href={`/projects/task/${project_id}/delete/${id}`}>Delete</a></li>
                    </ul>
                </div>
            </div>
            <div className="clearfix"/>
        </div>
    );
});

const List = SortableContainer(({items, updateStatus, updateDate, onAddNewClick, onTaskClick, onEdit, onDelete,onSortEnd}) => {
    return (
        <div>
            {items.map((item, index) => (
                <Item key={`main-task-${item.id}`}
                      index={index}
                      updateStatus={updateStatus}
                      updateDate={updateDate}
                      onAddNewClick={onAddNewClick}
                      onTaskClick={onTaskClick}
                      onEdit={onEdit}
                      onDelete={onDelete}
                      onSortEnd={onSortEnd}
                      {...item} />
            ))}
        </div>
    );
});

export default class TasksList extends Component {

    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.sortingInterval = null;
        this.state = {
            items: this.props.items,
            task_id: null,
            visibleAddNewModal: false,
            forceModalClose: false
        };
    }

    componentDidMount() {
        //this.sortingInterval = setInterval(this.saveSorting, 3000);
        const {items} = this.state;

        this.updateState({
            items: this.order(items)
        });
    }

    // componnentWillReceiveProps(props) {
    //     this.updateState({
    //         items: this.order(props.items);
    //     });
    // }

    componentWillUnmount() {
        this.isUnmounted = true;
        //clearInterval(this.sortingInterval);
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    saveSorting() {
        const {items} = this.state;
        const {id} = this.props;

        const orderedItems = items.map(item => {
            return item.id;
        });

        let postData = new FormData();

        postData.set(app.token_name, app.token);
        postData.set('tasks', JSON.stringify(orderedItems));

        API.post(`projects/tasks/${id}/order`, postData, {
            headers: {'Content-Type': 'multipart/form-data'}
        });
    }

    onSortEnd(index) {
        const {oldIndex, newIndex} = index;

        this.updateState({
            items: arrayMove(this.state.items, oldIndex, newIndex),
        }, () => {
            const {items} = this.state;

            this.updateState({
                items: this.order(items)
            }, () => this.saveSorting());
        });
    };

    order(items) {
        let new_items = [];

        items.map(i => {
            if (i.status === 'open') {
                new_items.push(i);
            }
        });

        items.map(i => {
            if (i.status === 'done') {
                new_items.push(i);
            }
        });
        return new_items;
    }

    updateStatus(id, status) {
        const {items} = this.state;

        let newItems = items.map(item => {
            if (item.id === id) {
                item.status = status;
            }
            return item;
        });

        const orderItems = this.order(newItems);
        this.updateState({
            items: orderItems
        });

        var postData = new FormData();

        postData.set(app.token_name, app.token);

        API.post(`projects/tasks/${id}/check`, postData, {
            headers: {'Content-Type': 'multipart/form-data'}
        });
    }

    updateDate(id, date) {
        const {items} = this.state;
        const due_date = Util.getDateStr(date[0]);

        let newItems = items.map(item => {
            if (item.id === id) {
                item.due_date = due_date;
            }
            return item;
        });

        this.updateState({
            items: newItems
        });

        var postData = new FormData();

        postData.set(app.token_name, app.token);
        postData.set('due_date', due_date);

        API.post(`projects/tasks/${id}/update`, postData, {
            headers: {'Content-Type': 'multipart/form-data'}
        });
    }

    onAddNewClick(e, id) {
        e.preventDefault();

        this.updateState({
            task_id: id,
            visibleAddNewModal: true
        });
    }

    onNewItemFormSubmitted(data) {
        const {onReload} = this.props;

        this.updateState({
            forceModalClose: true
        });

        onReload();
    }

    onTaskClick(item) {
        const {onTaskClick} = this.props;

        onTaskClick(item);
    }

    onDelete() {

    }

    onEdit() {

    }

    render() {
        const {project_id} = this.props;
        const {items, visibleAddNewModal, forceModalClose, task_id} = this.state;

        return (
            <div>
                {visibleAddNewModal &&
                <Modal title="New Assign"
                       forceClose={forceModalClose}
                       onRequestClose={() => this.updateState({visibleAddNewModal: false, forceModalClose: false})}>
                    <AssignWorkerForm
                        task_id={task_id}
                        beforeSubmit={() => {
                        }}
                        onSubmitted={(d) => this.onNewItemFormSubmitted(d)}/>
                </Modal>
                }
                <List items={items}
                      updateStatus={this.updateStatus.bind(this)}
                      updateDate={this.updateDate.bind(this)}
                      onSortEnd={this.onSortEnd.bind(this)}
                      onAddNewClick={this.onAddNewClick.bind(this)}
                      onTaskClick={this.onTaskClick.bind(this)}
                      onEdit={this.onEdit.bind(this)}
                      onDelete={this.onDelete.bind(this)}
                      useDragHandle={true}/>
            </div>
        );
    }
}