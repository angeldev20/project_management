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
import AddNewGrid from "./AddNewGrid";
import Grid from "./Grid";
import PeopleGridItem from "./PeopleGridItem";
import EmptyContent from "./EmptyContent";
import TeamMemberForm from "./TeamMemberForm";
import Notify from "./Notify";
import DeleteModal from "./DeleteModal";

export default class PeopleTeam extends Component {

    constructor(props) {
        super(props);

        const {id} = props.match.params;

        this.isUnmounted = false;
        this.state = {
            redirect: false,
            loading: true,
            id: id,
            people: [],
            visibleAddNewModal: false,
            forceModalClose: false,
            notify: false,
            notifyType: null,
            notifyMessage: null
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
        this.updateState({
            loading: true
        });

        API.get(`team/data`)
            .then(res => res.data)
            .then(data => this.initialize(data));
    }

    reloadData() {
        this.updateState({
            loading: true
        });
        API.get(`team/data`)
            .then(res => res.data)
            .then(data => this.initialize(data));
    }


    initialize(data) {
        if (data.status) {
            this.updateState({
                people: data.users,
                loading: false
            });
        }
    }

    onRequestError(e) {
        console.error(e);
    }

    onAddNewClick(e) {
        e.preventDefault();

        //$("#add-new-button").trigger("click");

        this.updateState({
            visibleAddNewModal: true
        });
    }

    onEditFormSubmitted(data) {
        if (data.status) {
            this.updateState({
                notify: true,
                notifyType: 'success',
                notifyMessage: 'Invitation sent.',
                forceModalClose: true
            });
        } else {
            this.updateState({
                notify: true,
                notifyType: 'danger',
                notifyMessage: 'Failed to send invitation.',
                forceModalClose: true
            });
        }
    }

    render() {
        const {
            id,
            redirect,
            loading,
            people,
            visibleAddNewModal,
            forceModalClose,
            notify,
            notifyType,
            notifyMessage
        } = this.state;

        if (redirect) {
            return <Redirect to={redirect} from="/team"/>
        }

        return (
            <div>
                <HeaderTabs {...this.props} tabs={Constants.PeopleTabs}/>
                <DeleteModal onItemDelete={data => this.getData()} trigger=".people-delete-trigger"
                             title="Delete Member"
                             text="Are you sure to remove this member from team?"/>
                {visibleAddNewModal &&
                <Modal title="New Team Member"
                       forceClose={forceModalClose}
                       onRequestClose={() => this.updateState({visibleAddNewModal: false, forceModalClose: false})}>
                    <TeamMemberForm
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
                <div>
                    <div className="col-sm-12 col-md-12 main">
                        <div className="row">
                            {people.length > 0 &&
                            <div>
                                <div className="tabb-header">
                                    <div className="col-md-6 table-header-left">
                                        <h2 className="page-title">All Team Members</h2>
                                    </div>
                                    <div className="col-md-6 text-right table-header-right">
                                        <div>
                                            <a href="#" className="transparent"><i
                                                className={"far fa-search"}/></a>
                                        </div>
                                        <div><a id="add-new-button" onClick={this.onAddNewClick.bind(this)}
                                                className="btn btn-success">Add New</a>
                                        </div>
                                    </div>
                                </div>
                                <div className="clearfix"/>
                                <div className="tabb-content">
                                    <Grid GridItem={(props) => <PeopleGridItem {...props} reload={this.reloadData.bind(this)} />}
                                          AddNewItem={(props) => <AddNewGrid {...props}/>}
                                          AddNewItemText="Add New"
                                          data={people}
                                          onAddNewClick={this.onAddNewClick.bind(this)}/>
                                </div>
                            </div>
                            }
                            {people.length <= 0 &&
                            <EmptyContent
                                title="You haven’t added any team member, yet!"
                                description="Add first team member."
                                button={
                                    <button onClick={this.onAddNewClick.bind(this)} className="btn btn-danger">Add
                                        First
                                        Member</button>
                                }/>
                            }
                        </div>
                    </div>
                </div>
                }
            </div>
        );
    }
}