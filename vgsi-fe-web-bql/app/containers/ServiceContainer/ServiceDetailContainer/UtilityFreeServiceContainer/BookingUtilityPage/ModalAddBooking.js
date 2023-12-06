/*
 * Created by duydatpham@gmail.com on 04/02/2020
 * Copyright (c) 2020 duydatpham@gmail.com
 */
import React from 'react'
import { Modal, Form, Input, Select, InputNumber, TimePicker, Row, Col, Spin, Checkbox } from 'antd'
import moment from 'moment';

import _ from 'lodash'
import { fetchApartmentAction } from './actions';

const formItemLayout = {
    labelCol: {
        xs: { span: 24 },
        sm: { span: 6 },
    },
    wrapperCol: {
        xs: { span: 24 },
        sm: { span: 18 },
    },
};
@Form.create()
export default class extends React.PureComponent {

    componentDidMount() {
        this.onSearch()
    }

    onSearch = (keyword = '') => {
        this.props.dispatch(fetchApartmentAction({ keyword }))
    };
    _onSearch = _.debounce(this.onSearch, 300);
    _onSave = (status, message) => {
        const { onSave, form } = this.props
        const { validateFieldsAndScroll, setFields } = form

        validateFieldsAndScroll((errors, values) => {
            if (errors)
                return
            !!onSave && onSave(values)
        })
    }

    componentWillReceiveProps(nextProps) {
        if (this.props.visible != nextProps.visible && !nextProps.visible) {
            this.props.form.resetFields()
            this.onSearch('')
        }
    }

    render() {
        const { apartments, currentDaySelected, slots } = this.props
        const { getFieldDecorator, getFieldsError, getFieldValue } = this.props.form;
        return <Modal
            {...this.props}
            onOk={this._onSave}
        >

            <Form >
                <Form.Item
                    label={`Căn hộ`}
                    style={{ marginTop: 0, marginBottom: 0 }}
                >
                    {getFieldDecorator('apartment_id', {
                        // initialValue: '',
                        rules: [{ required: true, message: 'Căn hộ không được để trống.', whitespace: true }],
                    })(
                        <Select
                            style={{ width: '100%' }}
                            loading={apartments.loading}
                            showSearch
                            placeholder="Chọn căn hộ"
                            optionFilterProp="children"
                            notFoundContent={apartments.loading ? <Spin size="small" /> : null}
                            onSearch={this._onSearch}
                            allowClear
                        >
                            {
                                apartments.lst.map(gr => {
                                    return <Select.Option key={`group-${gr.id}`} value={`${gr.id}`}>{`${gr.name} (${gr.parent_path})`}</Select.Option>
                                })
                            }
                        </Select>
                    )}
                </Form.Item>
                {/* <Row>
                    <Col span={11} >
                        <Form.Item
                            label={`Thời gian bắt đầu`}
                            style={{ marginTop: 0, marginBottom: 0 }}
                        >
                            {getFieldDecorator('start_time',
                                {
                                    initialValue: moment('08:00', 'HH:mm'),
                                    rules: [{ type: 'object', required: true, message: 'Thời gian bắt đầu không được để trống.' }],
                                })(<TimePicker style={{ width: '100%' }} format='HH:mm' />)}
                        </Form.Item>
                    </Col>
                    <Col span={11} offset={2} >
                        <Form.Item
                            label={`Thời gian kết thúc`}
                            style={{ marginTop: 0, marginBottom: 0 }}
                        >
                            {getFieldDecorator('end_time',
                                {
                                    initialValue: moment('09:00', 'HH:mm'),
                                    rules: [{ type: 'object', required: true, message: 'Thời gian kết thúc không được để trống.' }],
                                })(<TimePicker style={{ width: '100%' }} format='HH:mm'
                                />)}
                        </Form.Item>
                    </Col>
                </Row> */}
                <Form.Item label="Thời gian sử dụng"
                    style={{ marginTop: 0, marginBottom: 0 }}
                >
                    {getFieldDecorator('time_use', {
                        initialValue: [],
                        rules: [{ type: 'array', required: true, message: 'Thời gian sử dụng phải chọn tối thiểu 1 khoảng.' }],
                    })(
                        <Checkbox.Group style={{ width: '100%' }}>
                            <Row>
                                {
                                    !!slots && slots.items.map((ii, i) => {
                                        return <Col span={12} key={`row-${i}`} >
                                            <Checkbox disabled={ii.slot_null == 0} value={`${ii.start_time}-${ii.end_time}`}>{`${ii.start_time} - ${ii.end_time} `}(<span style={{ fontWeight: 'bold', color: 'black' }} >{ii.slot_null}</span> trống)</Checkbox>
                                        </Col>
                                    })
                                }
                            </Row>
                        </Checkbox.Group>,
                    )}
                </Form.Item>
                <Form.Item
                    label={`Số người`}
                    style={{ marginTop: 0, marginBottom: 0 }}
                >
                    {getFieldDecorator('total_adult', {
                        initialValue: '',
                        rules: [{ required: true, message: 'Số người không được để trống.', whitespace: true, type: 'number' }],
                    })(
                        <InputNumber style={{ width: '100%' }} maxLength={50} />
                    )}
                </Form.Item>
                <Form.Item
                    label={`Ghi chú`}
                    style={{ marginTop: 0, marginBottom: 0 }}
                >
                    {getFieldDecorator('description', {
                        initialValue: '',
                    })(
                        <Input.TextArea style={{ width: '100%' }}
                            rows={4}
                        />
                    )}
                </Form.Item>
            </Form>
        </Modal>
    }
}
