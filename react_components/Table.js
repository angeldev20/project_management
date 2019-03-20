import React, {Component} from 'react';
import Checkbox from "./Checkbox";

export default class Table extends Component {

    constructor(props) {
        super(props);

        this.id = 'table-' + Math.floor((Math.random() * 100) + 1);
    }

    onCheckAll(e) {
        var $elem = $(`#${this.id}`);
        var isChecked = $elem.find('.check-all').is(":checked");

        $elem.find("tbody input[type='checkbox']").prop("checked", isChecked);
        $elem.find("tbody input[type='checkbox']").trigger("change");
    }

    onRowClick(e) {
    }

    render() {
        const {headings, renderItem, data, showCheckboxes} = this.props;

        return (
            <div id={this.id} className="table-div shadow-box">
                <table className="data table">
                    <thead>
                    <tr>
                        {showCheckboxes &&
                        <th className="no_sort text-center" width="50" style={{paddingRight: "inherit"}}>
                            <Checkbox checkboxAttributes={{className: "check-all"}}
                                      onCheckChange={this.onCheckAll.bind(this)}/>
                        </th>
                        }
                        {headings.map((heading, index) => (
                            <th key={index} {...heading.attributes}>{heading.text}</th>
                        ))}
                    </tr>
                    </thead>
                    <tbody>
                    {data.map((item, index) => {
                        return renderItem(item, index, showCheckboxes, this.onRowClick);
                    })}
                    </tbody>
                </table>
            </div>
        );
    }
}