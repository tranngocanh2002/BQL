
import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { FormattedMessage } from "react-intl";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectApartmentDetail from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import messages from "./messages";
import { Row, Col, Table, Tooltip, Icon, Modal, Button, TreeSelect, InputNumber, Input, Form, Select, Spin, DatePicker } from "antd";
import { config } from "../../../../../utils";
import { fetchApartment } from "./actions";
import moment from "moment";
import NumericInput from '../../../../../components/NumericInput'
const monthFormat = 'MM/YYYY';
const { MonthPicker, RangePicker } = DatePicker;
import('./index.less')
const formItemLayout = {
    labelCol: {
        span: 8,
    },
    wrapperCol: {
        span: 12,
    },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export default class extends React.PureComponent {
    constructor(props) {
        super(props)
        this.state = {
            showPickerColor: false,
        }
        this._onSearch = _.debounce(this.onSearch, 300);
    }

    onSearch = keyword => {
        this.props.dispatch(fetchApartment({ name: keyword }))
    }
    componentDidMount() {
        this._onSearch('')
    }

    handlerUpdate = () => {
        const { currentEdit, form } = this.props
        const { validateFieldsAndScroll } = form
        validateFieldsAndScroll((errors, values) => {
            if (errors) {
                return
            }
            let apartment_id = values.apartment_id.split(':')
            if (currentEdit) {
                this.props.updatePayment && this.props.updatePayment({
                    ...values,
                    status: 0,
                    price: parseInt(values.price),
                    fee_of_month: values.fee_of_month.unix(),
                    day_expired: values.day_expired.unix(),
                    apartment_id: apartment_id[0]
                })
            } else {
                this.props.addPayment && this.props.addPayment({
                    ...values,
                    status: 0,
                    price: parseInt(values.price),
                    fee_of_month: values.fee_of_month.unix(),
                    day_expired: values.day_expired.unix(),
                    apartment_id: apartment_id[0]
                })
            }
        })
    }

    componentWillReceiveProps(nextProps) {
        if (this.props.visible != nextProps.visible) {
            this.props.form.resetFields()
            if (nextProps.visible) {
                this._onSearch(nextProps.currentEdit ? nextProps.currentEdit.apartment_name : '')
            }
        }
    }

    render() {
        const { showPickerColor } = this.state
        const { visible, setState, paymentManagementClusterPage, currentEdit } = this.props
        const { getFieldDecorator, getFieldValue, setFieldsValue } = this.props.form;
        return <Modal
            title={!!currentEdit ? "Chỉnh sửa phí thanh toán" : "Tạo phí thanh toán"}
            visible={visible}
            onOk={this.handlerUpdate}
            onCancel={() => {
                if (paymentManagementClusterPage.creating)
                    return
                setState({
                    visible: false
                })
            }}
            okText={!!currentEdit ? 'Cập nhật' : 'Thêm mới'}
            cancelText='Huỷ'
            okButtonProps={{ loading: paymentManagementClusterPage.creating }}
            cancelButtonProps={{ disabled: paymentManagementClusterPage.creating }}
            maskClosable={false}
        >

            <Form {...formItemLayout} className='ticketCategoryPage'  >
                <Form.Item
                    label={'Căn hộ'}
                >
                    {getFieldDecorator('apartment_id', {
                        initialValue: !!currentEdit ? `${currentEdit.apartment_id}:1` : undefined,
                        rules: [
                            { required: true, message: 'Căn hộ không được để trống.', whitespace: true },
                            {
                                validator: (rule, value, callback) => {
                                    const form = this.props.form;
                                    if (value) {
                                        let values = value.split(':')
                                        if (values.length == 2 && values[1] == 0) {
                                            callback('Căn hộ được chọn đang trống.');
                                        } else {
                                            callback();
                                        }
                                    } else {
                                        callback();
                                    }
                                }
                            }
                        ],
                    })(
                        <Select
                            loading={paymentManagementClusterPage.apartment.loading}
                            showSearch
                            placeholder="Chọn căn hộ"
                            optionFilterProp="children"
                            notFoundContent={paymentManagementClusterPage.apartment.loading ? <Spin size="small" /> : null}
                            onSearch={this._onSearch}
                        >
                            {
                                paymentManagementClusterPage.apartment.items.map(gr => {
                                    return <Select.Option key={`group-${gr.id}`} value={`${gr.id}:${gr.status}`}
                                    >{`${gr.name} (${gr.parent_path})${gr.status == 0 ? ' - Trống' : ''}`}</Select.Option>
                                })
                            }
                        </Select>
                    )}
                </Form.Item>
                <Form.Item
                    label={'Mô tả'}
                >
                    {getFieldDecorator('description', {
                        initialValue: !!currentEdit ? currentEdit.description : undefined,
                        rules: [
                        ],
                    })(
                        <Input.TextArea rows={4} maxLength={500} />
                    )}
                </Form.Item>
                <Form.Item
                    label={'Số tiền'}
                >
                    {getFieldDecorator('price', {
                        initialValue: !!currentEdit ? `${currentEdit.price}` : undefined,
                        rules: [{ required: true, message: 'Số tiền không được để trống.', whitespace: true }],
                    })(
                        <NumericInput maxLength={10} />
                    )}
                </Form.Item>
                <Form.Item
                    label={'Tháng'}
                >
                    {getFieldDecorator('fee_of_month', {
                        initialValue: !!currentEdit ? moment.unix(currentEdit.fee_of_month) : undefined,
                        rules: [{ type: 'object', required: true, message: 'Tháng thanh toán không được để trống.' }],
                    })(<MonthPicker style={{ width: '100%' }}
                        placeholder='Chọn tháng'
                        format={monthFormat}
                    />)}
                </Form.Item>
                <Form.Item
                    label={'Hạn thanh toán'}
                >
                    {getFieldDecorator('day_expired', {
                        initialValue: !!currentEdit ? moment.unix(currentEdit.day_expired) : undefined,
                        rules: [{ type: 'object', required: true, message: 'Hạn thanh toán không được để trống.' }],
                    })(<DatePicker style={{ width: '100%' }}
                        placeholder='Chọn ngày'
                        format={'DD/MM/YYYY'}
                    />)}
                </Form.Item>
                {/* <Form.Item
                    label={'Trạng thái'}
                >
                    {getFieldDecorator('status', {
                        initialValue: !!currentEdit ? currentEdit.status : 0,
                        rules: [{ required: true, message: 'Trạng thái không được để trống.', whitespace: true, type: 'number' }],
                    })(
                        <Select
                            showSearch
                            placeholder="Chọn trạng thái"
                            optionFilterProp="children"
                            // onChange={onChange}
                            filterOption={(input, option) =>
                                option.props.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
                            }
                        >
                            {
                                config.STATUS_SERVICE_PAYMENT.map(gr => {
                                    return <Select.Option key={`group-${gr.id}`} value={gr.id}>{gr.name}</Select.Option>
                                })
                            }
                        </Select>
                    )}
                </Form.Item> */}
            </Form>
        </Modal>
    }
}