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
import DeleteModal from "./DeleteModal";

export default class ProjectTeam extends Component {

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
            forceModalClose: false
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

        API.get(`projects/get/${id}/workers`)
            .then(res => res.data)
            .then(data => this.initialize(data));
    }

    initialize(data) {
        if (data.status) {
            this.updateState({
                people: data.data,
                loading: false
            });
        }
    }

    onRequestError(e) {
        console.error(e);
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

        $("#add-new-button").trigger("click");
    }

    render() {
        const {
            id,
            redirect,
            loading,
            people,
            visibleAddNewModal,
            forceModalClose,
        } = this.state;

        if (redirect) {
            return <Redirect to={redirect} from="/projects"/>
        }

        return (
            <div>
                <HeaderTabs {...this.props} tabs={Constants.ProjectTabs(id)}/>
                <DeleteModal onItemDelete={data => this.getData()} trigger=".people-delete-trigger"
                             title="Delete Member"
                             text="Are you sure to remove this member from team?"/>
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
                                        <h2 className="page-title">Project Team Members</h2>
                                    </div>
                                    <div className="col-md-6 text-right table-header-right">
                                        <div>
                                            <a href="#" className="transparent"><i
                                                className={"far fa-search"}/></a>
                                        </div>
                                        <div><a id="add-new-button" href={`/projects/assign/${id}`}
                                                data-toggle="mainmodal"
                                                className="btn btn-success">Add New</a>
                                        </div>
                                    </div>
                                </div>
                                <div className="clearfix"/>
                                <div className="tabb-content">
                                    <Grid GridItem={(props) => <PeopleGridItem {...props}/>}
                                          AddNewItem={(props) => <AddNewGrid {...props}/>}
                                          AddNewItemText="Add New"
                                          data={people}
                                          onAddNewClick={this.onAddNewClick.bind(this)}/>
                                </div>
                            </div>
                            }
                            {people.length <= 0 &&
                            <EmptyContent
                                title="You haven’t added any team member to this project, yet!"
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