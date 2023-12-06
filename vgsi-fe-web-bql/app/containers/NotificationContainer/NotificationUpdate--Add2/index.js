/**
 *
 * NotificationUpdate
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { FormattedMessage } from "react-intl";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectNotificationUpdate from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import messages from "./messages";

import { EditorState, convertToRaw, convertFromHTML, ContentState } from 'draft-js';
import draftToHtml from 'draftjs-to-html';
import htmlToDraft from 'html-to-draftjs';


import { Editor } from 'react-draft-wysiwyg'
import 'react-draft-wysiwyg/dist/react-draft-wysiwyg.css'
import Page from "../../../components/Page/Page";
import { Col, Row, Form, Input, Select, Button, Steps, TreeSelect, InputNumber, Icon, Spin, Tooltip, Checkbox, DatePicker } from "antd";
import Upload from '../../../components/Uploader'
import './index.less'
import { fetchBuildingAreaAction, fetchUltilityAction, defaultAction, createNotificationAction, fetchTotalApartmentAction, updateNotificationAction, fetchDetailNotification } from "./actions";
import { parseTree, config } from "../../../utils";
import { selectBuildingCluster, selectAuthGroup } from "../../../redux/selectors";

import { Redirect, withRouter } from "react-router";
import WithRole from "../../../components/WithRole";

import moment from 'moment'
import { GLOBAL_COLOR } from "../../../utils/constants";
import { CUSTOM_TOOLBAR } from "../../../utils/config";

const formItemLayout = {
  labelCol: {
    xl: { span: 6 },
  },
  wrapperCol: {
    xl: { span: 18 },
  },
};


const Step = Steps.Step;
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class NotificationUpdate extends React.PureComponent {
  constructor(props) {
    super(props)
    const { record } = props.location.state || {}

    let blockArray = !!record ? convertFromHTML(record.content) : undefined

    this.state = {
      record,
      editorState: !!blockArray && !!blockArray.contentBlocks ? EditorState.createWithContent(ContentState.createFromBlockArray(blockArray)) : EditorState.createEmpty(),
      treeData: parseTree(props.buildingCluster.data, props.notificationUpdate.buildingArea.lst.map(node => ({ key: `${node.id}`, title: node.name, value: `${node.id}`, ...node, children: [] }))),
      fileImageList: !!record && !!record.attach && !!record.attach.fileImageList ? record.attach.fileImageList : [],
      fileList: !!record && !!record.attach && !!record.attach.fileList ? record.attach.fileList : [],
      is_event: !!record && record.is_event == 1,
      is_send_at: !!record && !!record.send_at && (record.status + 1 == 2 || moment.unix(record.send_at) > moment()) ? 1 : 0,
      currentTimeSent: !!record && !!record.send_at ? moment.unix(record.send_at) : moment()
    }
    if (!!record) {
      this.props.dispatch(fetchTotalApartmentAction({ ids: record.building_area_ids }))
    }
  }

  onEditorStateChange = (editorState) => {
    this.setState({
      editorState,
    });
  };


  componentWillUnmount() {
    this.props.dispatch(defaultAction())
  }

  componentDidMount() {
    this.props.dispatch(fetchUltilityAction())
    const { id } = this.props.match.params
    if (id != undefined && !this.state.record) {
      this.props.dispatch(fetchDetailNotification({ id }))
    }
  }

  componentWillReceiveProps(nextProps) {
    if (this.props.notificationUpdate.buildingArea.loading != nextProps.notificationUpdate.buildingArea.loading && !nextProps.notificationUpdate.buildingArea.loading) {
      this.setState({
        treeData: parseTree(this.props.buildingCluster.data, nextProps.notificationUpdate.buildingArea.lst.map(node => ({ key: `${node.id}`, title: node.name, value: `${node.id}`, ...node, children: [] })))
      })
    }
    if (this.props.notificationUpdate.detail.loading != nextProps.notificationUpdate.detail.loading && !nextProps.notificationUpdate.detail.loading) {
      const record = nextProps.notificationUpdate.detail.data

      let blockArray = !!record ? convertFromHTML(record.content) : undefined

      this.setState({
        record,
        editorState: !!blockArray && !!blockArray.contentBlocks ? EditorState.createWithContent(ContentState.createFromBlockArray(blockArray)) : EditorState.createEmpty(),
        fileImageList: !!record && !!record.attach && !!record.attach.fileImageList ? record.attach.fileImageList : [],
        fileList: !!record && !!record.attach && !!record.attach.fileList ? record.attach.fileList : [],
        is_event: !!record && record.is_event == 1,
        is_send_at: !!record && !!record.send_at ? 1 : 0,
        currentTimeSent: !!record && !!record.send_at ? moment.unix(record.send_at) : moment()
      })
      if (!!record) {
        this.props.dispatch(fetchTotalApartmentAction({ ids: record.building_area_ids }))
      }
    }
  }


  handleOk = (status, message) => {
    const { dispatch, form, notificationUpdate } = this.props
    const { validateFieldsAndScroll, setFields } = form


    let contentRaw = convertToRaw(this.state.editorState.getCurrentContent())
    let isErrorContent = false
    if (!!!contentRaw || !!!contentRaw.blocks || !contentRaw.blocks.some(block => block.text.replace(/ /g, '').length != 0)) {
      setFields({
        content: {
          value: '',
        }
      })
      isErrorContent = true
    } else {
      setFields({
        content: {
          value: '111',
          errors: []
        }
      })
      isErrorContent = false
    }

    validateFieldsAndScroll((errors, values) => {
      if (!isErrorContent) {
        if (_.sum(contentRaw.blocks.map(bl => bl.text.length)) > 2000) {
          setFields({
            content: {
              value: '111',
              errors: [new Error('Nội dung không được dài quá 2000 ký tự.')]
            }
          })
          isErrorContent = true
        }
      }
      if (errors || isErrorContent) {
        return
      }


      const { record, is_event } = this.state
      if (!!record) {
        dispatch(updateNotificationAction({
          id: record.id,
          title: values.title,
          building_area_ids: notificationUpdate.totalApartment.building_area_ids, //values.building_area_ids.map(r => parseInt(r)),
          announcement_category_id: parseInt(values.announcement_category_id),
          content: draftToHtml(contentRaw),
          status,
          attach: {
            fileImageList: this.state.fileImageList,
            fileList: this.state.fileList,
          },
          message,
          is_event: is_event ? 1 : 0,
          send_at: !!values.send_at ? values.send_at.unix() : undefined,
          send_event_at: !!values.send_event_at ? values.send_event_at.unix() : undefined,
        }))
      } else {
        dispatch(createNotificationAction({
          title: values.title,
          // building_area_ids: values.building_area_ids.map(r => parseInt(r)),
          building_area_ids: notificationUpdate.totalApartment.building_area_ids,
          announcement_category_id: parseInt(values.announcement_category_id),
          content: draftToHtml(contentRaw),
          status,
          attach: {
            fileImageList: this.state.fileImageList,
            fileList: this.state.fileList,
          },
          message,
          is_event: is_event ? 1 : 0,
          send_at: !!values.send_at ? values.send_at.unix() : undefined,
          send_event_at: !!values.send_event_at ? values.send_event_at.unix() : undefined,
        }))
      }
    })
  }


  render() {
    const { editorState, treeData, record } = this.state;
    const { notificationUpdate, auth_group } = this.props
    const { getFieldDecorator, getFieldsError } = this.props.form;
    const tProps = {
      treeData,
      treeCheckable: true,
      showCheckedStrategy: TreeSelect.SHOW_PARENT,
      treeDefaultExpandAll: true,
      searchPlaceholder: 'Chọn danh sách để gửi',
      loading: true,
    };

    const { category, buildingArea, creating, createSuccess, totalApartment, detail } = notificationUpdate

    if (createSuccess) {
      return <Redirect to='/main/notification/list' />
    }

    const errorCurrent = getFieldsError(['content'])

    const currentStep = !!record ? record.status + 1 : 0

    const RowTreeSelect = props => {
      return <Row type='flex' >
        <TreeSelect
          {...tProps}
          disabled={!canCreateOrUpdate || currentStep == 2}
          value={props.value}
          style={{
            width: '50%', marginRight: '1%'
          }}
          onChange={value => {
            console.log('values', value)
            let ids = []
            if (value.length == 1 && value[0] == -1) {
              ids = this.props.notificationUpdate.buildingArea.lst.filter(ddd => !ddd.parent_id).map(ddd => ddd.id)
            } else {
              ids = value.map(vv => parseInt(vv))
            }
            this.props.dispatch(fetchTotalApartmentAction({ ids }))
            props.onChange(value)
          }} />
        <Row type='flex' align='middle' justify='center' style={{
          background: '#EFF1F4', height: 32,
          borderRadius: 4, border: '1px solid #CACBD4',
          lineHeight: 0,
          paddingTop: 4,
          width: '49%'
        }}
        >
          Gửi tới&ensp;{totalApartment.loading ? <Spin size="small" /> : <span style={{ color: GLOBAL_COLOR, fontWeight: 'bold' }} >{totalApartment.total}</span>}&ensp;căn hộ
          </Row>
      </Row>
    }

    const canCreateOrUpdate = auth_group.checkRole([config.ALL_ROLE_NAME.ANNOUNCE_CREATE_UPDATE])

    return (
      <Page inner loading={detail.loading} >
        <div>
          <Row gutter={24}>
            <Col lg={{ span: 14, offset: 5 }} md={24} >
              <Steps size="small" current={currentStep}>
                <Step title="Tạo mới" />
                <Step title="Lưu nháp" />
                <Step title="Công khai" />
              </Steps>
            </Col>
          </Row>
          <Row className={'notificationUpdatePage'} gutter={24} style={{ marginTop: 40 }} >
            <Col lg={{ span: 18, offset: 3 }} md={24} >
              <Form {...formItemLayout} onSubmit={this.handleSubmit}>
                <Form.Item
                  label={`Tiêu đề`}
                >
                  {getFieldDecorator('title', {
                    initialValue: !!record ? record.title : '',
                    rules: [{ required: true, message: 'Tiêu đề không được để trống.', whitespace: true }],
                  })(<Input style={{ width: '100%' }} disabled={!canCreateOrUpdate} maxLength={150} />)}
                </Form.Item>
                <Form.Item
                  label={'Nội dung'}
                >
                  {getFieldDecorator('content', {
                    rules: [{ required: true, message: 'Nội dung không được để trống.' }],
                  })(
                    <div style={{ border: !!errorCurrent.content ? '1px solid red' : '' }} >
                      <Editor
                        editorState={editorState}
                        wrapperClassName="demo-wrapper"
                        editorClassName="rdw-storybook-editor"
                        onEditorStateChange={this.onEditorStateChange}
                        handleBeforeInput={(input) => {
                          if (_.sum(convertToRaw(this.state.editorState.getCurrentContent()).blocks.map(bl => bl.text.length)) >= 2000) {
                            return 'handled';
                          }
                        }}
                        toolbar={CUSTOM_TOOLBAR}
                      />
                    </div>
                  )}
                </Form.Item>
                <Form.Item
                  label={'Danh mục'}
                >
                  {getFieldDecorator('announcement_category_id', {
                    initialValue: !!record ? String(record.announcement_category_id) : undefined,
                    rules: [{ required: true, message: 'Danh mục không được để trống.', whitespace: true }],
                  })(
                    <Select loading={category.loading}
                      disabled={!canCreateOrUpdate}
                      showSearch
                      placeholder="Chọn danh mục thông báo"
                      optionFilterProp="children"
                      // onChange={onChange}
                      filterOption={(input, option) =>
                        option.props.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
                      }
                    >
                      {
                        category.lst.map(gr => {
                          return <Select.Option key={`group-${gr.id}`} value={`${gr.id}`}>{gr.name}</Select.Option>
                        })
                      }
                    </Select>
                  )}
                </Form.Item>
                <Form.Item
                  label={'Gửi tới'}
                >
                  {getFieldDecorator('building_area_ids', {
                    initialValue: !!record ? record.building_area_ids.map(r => String(r)) : [],
                    rules: [{ required: true, message: 'Gửi tới không được để trống.', type: 'array' }],
                  })(
                    <RowTreeSelect />
                  )}
                </Form.Item>
                <Form.Item
                  label={'Công khai'}
                >
                  <Row type='flex' align='middle' >
                    <Select
                      disabled={!canCreateOrUpdate || currentStep == 2}
                      value={this.state.is_send_at}
                      style={{ width: '50%', marginRight: '1%', marginBottom: window.innerWidth > 1440 ? null : 8 }}
                      onChange={e => {
                        this.setState({
                          is_send_at: e
                        })
                      }}
                    >
                      {
                        [
                          {
                            id: 0,
                            title: 'Công khai ngay'
                          },
                          {
                            id: 1,
                            title: 'Công khai vào lúc'
                          },
                        ].map(gr => {
                          return <Select.Option key={`group-${gr.id}`} value={gr.id}>{gr.title}</Select.Option>
                        })
                      }
                    </Select>
                    {
                      this.state.is_send_at == 1 && getFieldDecorator('send_at', {
                        initialValue: this.state.currentTimeSent,
                        rules: [{ required: true, message: 'Thời gian công khai không được để trống.', type: 'object' }],
                      })(
                        <DatePicker showTime format='HH:mm - DD/MM/YYYY'
                          disabled={!canCreateOrUpdate || currentStep == 2}
                          style={{ width: '49%' }}
                          disabledDate={(current) => {
                            // Can not select days before today and today
                            return current && current < moment().startOf('day');
                          }}
                          disabledTime={(current) => {
                            if (!!!current)
                              return {}
                            let now = moment()
                            if (current > moment().endOf('day')) {
                              return {}
                            }
                            return {
                              disabledHours: () => _.range(0, now.hour()),
                              disabledMinutes: current.hour() == now.hour() ? () => _.range(0, now.minute()) : () => ([]),
                              disabledSeconds: current.hour() == now.hour() && current.minute() == now.minute() ? () => _.range(0, now.second()) : () => ([]),
                            }
                          }}
                        />
                      )
                    }
                  </Row>
                </Form.Item>
                <Form.Item
                  label={<span>{`Sự kiện `}<Tooltip
                    title={'Thời điểm diễn ra sự kiện, hệ thống sẽ thông báo trước 1 ngày diễn ra sự kiện cho cư dân'}
                  >
                    <Icon type="info-circle-o" />
                  </Tooltip></span>}
                >
                  <Checkbox checked={this.state.is_event}
                    disabled={!canCreateOrUpdate || currentStep == 2}
                    onChange={value => {
                      console.log(`value`, value.target.checked)
                      this.setState({
                        is_event: value.target.checked
                      })
                    }} />
                  {
                    this.state.is_event && getFieldDecorator('send_event_at', {
                      initialValue: !!record && !!record.send_event_at ? moment.unix(record.send_event_at) : moment(),
                      rules: [{ required: true, message: 'Thời gian sự kiện không được để trống.', type: 'object' }],
                    })(
                      <DatePicker showTime format='HH:mm - DD/MM/YYYY'
                        disabled={!canCreateOrUpdate || currentStep == 2}
                        disabledDate={(current) => {
                          // Can not select days before today and today
                          return current && current < moment().startOf('day');
                        }}
                        disabledTime={(current) => {
                          if (!!!current)
                            return {}
                          let now = moment()
                          if (current > moment().endOf('day')) {
                            return {}
                          }
                          return {
                            disabledHours: () => _.range(0, now.hour()),
                            disabledMinutes: current.hour() == now.hour() ? () => _.range(0, now.minute()) : () => ([]),
                            disabledSeconds: current.hour() == now.hour() && current.minute() == now.minute() ? () => _.range(0, now.second()) : () => ([]),
                          }
                        }}
                      />
                    )
                  }
                </Form.Item>
                <Form.Item
                  label={'Ảnh đính kèm'}
                >
                  <Upload
                    disabled={!canCreateOrUpdate}
                    listType="picture-card"
                    showUploadList={true}
                    fileList={this.state.fileImageList}
                    acceptList={['image/']}
                    accept={'image/*'}
                    multiple
                    onRemove={file => {
                      this.setState({
                        fileImageList: this.state.fileImageList.filter(ff => ff.uid != file.uid)
                      })
                    }}
                    onUploaded={(url, file) => {
                      this.setState({
                        fileImageList: this.state.fileImageList.concat([{
                          uid: file.uid,
                          name: file.name,
                          status: 'done',
                          url
                        }])
                      })
                    }}
                  >
                    <Icon type="plus" />
                  </Upload>
                </Form.Item>
                <Form.Item
                  label={<span>{`Tệp đính kèm `}</span>}
                >
                  <Upload
                    disabled={!canCreateOrUpdate}
                    showUploadList={true}
                    fileList={this.state.fileList}
                    acceptList={[
                      "doc",
                      "docx",
                      "pdf",
                      "application/pdf",
                      "xls",
                      "xlsx",
                      "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                      "application/vnd.ms-excel",
                    ]}
                    multiple
                    onRemove={file => {
                      this.setState({
                        fileList: this.state.fileList.filter(ff => ff.uid != file.uid)
                      })
                    }}
                    onUploaded={(url, file) => {
                      this.setState({
                        fileList: this.state.fileList.concat([{
                          uid: file.uid,
                          name: file.name,
                          status: 'done',
                          url
                        }])
                      })
                    }}
                  >
                    <Button>
                      <Icon type="upload" /> Tải tệp
                    </Button>
                    <span style={{ marginLeft: 8 }}>
                      (Định dạng .doc, .docx, .pdf, .xls, .xlsx không vượt quá 25MB)
                    </span>
                  </Upload>
                </Form.Item>
              </Form>
            </Col>
            <WithRole roles={[config.ALL_ROLE_NAME.ANNOUNCE_CREATE_UPDATE]} >
              <Col lg={{ span: 18, offset: 3 }} md={24} >
                <Row gutter={24} >
                  <Col lg={4} md={0} sm={0} xs={0}>
                  </Col>
                  <Col lg={18} md={24} sm={24} xs={24}>
                    {/* <Button style={{ width: 120 }}
                      disabled={creating}
                      onClick={e => {
                      }} >
                      Xem trước
                  </Button> */}
                    {
                      currentStep == 0 &&
                      <>
                        <Button
                          loading={creating}
                          style={{ width: 120, marginLeft: 10 }}
                          onClick={() => {
                            this.handleOk(0, 'Lưu thông báo thành công.')
                          }}
                          disabled={totalApartment.loading}
                        >
                          Lưu nháp
                      </Button>
                        <Button //
                          loading={creating}
                          type='primary'
                          style={{ width: 120, marginLeft: 10 }}
                          onClick={() => {
                            this.handleOk(1, 'Công khai thông báo thành công.')
                          }}
                          disabled={totalApartment.loading}
                        >
                          Công khai
                    </Button>
                      </>
                    }
                    {
                      currentStep == 1 &&
                      <>
                        <Button
                          loading={creating}
                          style={{ width: 120, marginLeft: 10 }}
                          onClick={() => {
                            this.handleOk(0, 'Lưu thông báo thành công.')
                          }}
                          disabled={totalApartment.loading}
                        >
                          Lưu
                      </Button>
                        <Button //
                          loading={creating}
                          type='primary'
                          style={{ width: 120, marginLeft: 10 }}
                          onClick={() => {
                            this.handleOk(1, 'Công khai thông báo thành công.')
                          }}
                          disabled={totalApartment.loading}
                        >
                          Công khai
                    </Button>
                      </>
                    }
                    {
                      currentStep == 2 &&
                      <>
                        <Button //
                          loading={creating}
                          type='primary'
                          style={{ width: 120, marginLeft: 10 }}
                          onClick={() => {
                            this.handleOk(1, 'Lưu thông báo thành công.')
                          }}
                        >
                          Lưu
                    </Button>
                      </>
                    }
                  </Col>
                </Row>
              </Col>
            </WithRole>
          </Row>
        </div>
      </Page >
    );
  }
}

NotificationUpdate.propTypes = {
  dispatch: PropTypes.func.isRequired
};

const mapStateToProps = createStructuredSelector({
  notificationUpdate: makeSelectNotificationUpdate(),
  buildingCluster: selectBuildingCluster(),
  auth_group: selectAuthGroup()
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch
  };
}

const withConnect = connect(
  mapStateToProps,
  mapDispatchToProps
);

const withReducer = injectReducer({ key: "notificationUpdate", reducer });
const withSaga = injectSaga({ key: "notificationUpdate", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(NotificationUpdate));
