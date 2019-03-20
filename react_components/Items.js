import React, {Component} from 'react';
import {Link, Redirect} from "react-router-dom";
import API from './Api.js';
import HeaderTabs from "./HeaderTabs";
import * as Constants from "./Constants";
import AddNewGrid from "./AddNewGrid";
import EmptyContent from "./EmptyContent";
import ItemGridItem from "./ItemGridItem";
import Modal from "./Modal";
import Notify from "./Notify";
import Table from "./Table";
import ItemForm from "./ItemForm";
import DeleteModal from "./DeleteModal";

const Table2 = ({data, onDelete, onEdit}) => (
    <Table
        headings={[
            {
                text: "ID",
                attributes: {width: "70px", className: "hidden-xs"}
            },
            {
                text: "Type"
            },
            {
                text: "Name"
            },
            {
                text: "Description",
                attributes: {className: "hidden-xs"}
            },
            {
                text: "Amount",
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
        renderItem={({id, type, name, description, value}) => (
            <tr id={id} key={id}>
                <td className="hidden-xs">{id}</td>
                <td>{type}</td>
                <td>{name}</td>
                <td className="hidden-xs"><span>{description}</span></td>
                <td className="hidden-xs">${value.formatMoney()}</td>
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
                    <ItemGridItem key={props.id} {...props} />
                ))}
                <AddNewGrid onClick={onAddNewClick.bind(this)} icon="fal fa-plus-circle" text="Add an Item"/>
            </div>
        </div>
    </div>
);

export default class Items extends Component {
    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {
            view: 'grid',
            loading: true,
            items: [],
            redirect: false,
            visibleAddNewModal: false,
            forceModalClose: false,
            forceEditModalClose: false,
            notify: false,
            notifyType: '',
            notifyMessage: '',
            editing_id: null
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

    getData(loading = true) {
        this.updateState({
            loading: loading
        });

        API.get('items/data')
            .then(res => res.data)
            .then(data => this.initialize(data))
            .catch(e => this.onRequestError(e));
    }

    initialize(data) {
        this.updateState({
            items: data.items,
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

        API.get(format('items/delete_item/{0}', id))
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
        const {view, items, loading, redirect, status, visibleAddNewModal, forceModalClose, notify, notifyType, notifyMessage, visibleEditModal, forceEditModalClose, editing_id} = this.state;

        if (redirect) {
            return <Redirect to={redirect} from="/expenses"/>
        }

        return (
            <div>
                <HeaderTabs {...this.props} tabs={Constants.MoneyTabs}/>
                <DeleteModal onItemDelete={data => this.onDeleteFinish(data)} trigger=".item-delete-trigger"
                             title="Delete Project"
                             text="Are you sure to delete this item?"/>
                {visibleAddNewModal &&
                <Modal title="New Item"
                       forceClose={forceModalClose}
                       onRequestClose={() => this.updateState({visibleAddNewModal: false, forceModalClose: false})}>
                    <ItemForm
                        beforeSubmit={() => {
                        }}
                        onSubmitted={(d) => this.onNewItemFormSubmitted(d)}/>
                </Modal>
                }
                {visibleEditModal &&
                <Modal title="Edit Item"
                       forceClose={forceEditModalClose}
                       onRequestClose={() => this.updateState({visibleEditModal: false, forceEditModalClose: false})}>
                    <ItemForm
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
                        {items.length > 0 &&
                        <div>
                            <div className="tabb-header">
                                <div className="col-md-6 table-header-left">
                                    <h2 className="page-title">Products & Services</h2>
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
                                <Table2 data={items} onDelete={this.onDelete.bind(this)}
                                        onEdit={this.onEdit.bind(this)}/>
                                }
                                {this.state.view === 'grid' &&
                                <Grid data={items} onAddNewClick={this.onAddNewClick.bind(this)}/>
                                }
                            </div>
                        </div>
                        }
                        {items.length <= 0 &&
                        <EmptyContent
                            title="No products, yet!"
                            description="Add your first product."
                            button={
                                <button onClick={this.onAddNewClick.bind(this)} className="btn btn-danger">Add First
                                    Item</button>
                            }/>
                        }
                    </div>
                </div>
                }
            </div>
        );
    }
}