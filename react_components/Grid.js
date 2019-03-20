import React, {Component} from 'react';

export default class Grid extends Component {

    render() {
        const {GridItem, onAddNewClick, AddNewItem, data, AddNewItemText} = this.props;

        return (
            <div className="grids">
                <div className="row">
                    <div className="grid grid--align-content-start">
                        {data.map((props) => (
                            <GridItem key={props.id} {...props} />
                        ))}
                        <AddNewItem onClick={onAddNewClick.bind(this)} icon="fal fa-plus-circle" text={AddNewItemText}/>
                    </div>
                </div>
            </div>
        );
    }
}